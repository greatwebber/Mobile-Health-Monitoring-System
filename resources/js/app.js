import './bootstrap';
import '@hotwired/turbo';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

let statusTimer;

function showConnectionStatus(message, persistent = false) {
    const statusElement = document.getElementById('connection-status');

    if (!statusElement) return;

    window.clearTimeout(statusTimer);
    statusElement.textContent = message;
    statusElement.hidden = false;

    if (!persistent) {
        statusTimer = window.setTimeout(() => {
            statusElement.hidden = true;
        }, 3500);
    }
}

function updateConnectionStatus() {
    if (navigator.onLine) {
        showConnectionStatus('Connection restored.');
    } else {
        showConnectionStatus('You are offline. Some actions are unavailable.', true);
    }
}

window.addEventListener('online', updateConnectionStatus);
window.addEventListener('offline', updateConnectionStatus);

if (!navigator.onLine) updateConnectionStatus();

let navigationLoaderTimer;
let navigationPending = false;

function resetNavigationLoader() {
    const navigationLoader = document.getElementById('navigation-loader');
    const secureSessionIndicator = document.getElementById('secure-session-indicator');

    window.clearTimeout(navigationLoaderTimer);
    navigationPending = false;

    if (!navigationLoader) return;

    navigationLoader.hidden = true;
    document.body.removeAttribute('aria-busy');

    if (secureSessionIndicator) secureSessionIndicator.hidden = false;
}

function scheduleNavigationLoader() {
    const navigationLoader = document.getElementById('navigation-loader');

    if (!navigationLoader || navigationPending) return;

    navigationPending = true;
    navigationLoaderTimer = window.setTimeout(() => {
        const currentLoader = document.getElementById('navigation-loader');
        const currentSecureSessionIndicator = document.getElementById('secure-session-indicator');

        if (!currentLoader) return;

        currentLoader.hidden = false;
        document.body.setAttribute('aria-busy', 'true');

        if (currentSecureSessionIndicator) currentSecureSessionIndicator.hidden = true;
    }, 180);
}

function isInternalPageLink(anchor, event) {
    if (
        event.defaultPrevented
        || event.button !== 0
        || event.metaKey
        || event.ctrlKey
        || event.shiftKey
        || event.altKey
        || anchor.hasAttribute('download')
        || anchor.dataset.noNavigationLoader !== undefined
        || (anchor.target && anchor.target !== '_self')
    ) {
        return false;
    }

    const destination = new URL(anchor.href, window.location.href);
    const current = new URL(window.location.href);

    if (destination.origin !== current.origin || !['http:', 'https:'].includes(destination.protocol)) {
        return false;
    }

    const sameDocument = destination.pathname === current.pathname
        && destination.search === current.search;

    return !sameDocument;
}

document.addEventListener('click', (event) => {
    if (!(event.target instanceof Element)) return;

    const anchor = event.target.closest('a[href]');

    if (anchor && isInternalPageLink(anchor, event)) scheduleNavigationLoader();
});

document.addEventListener('submit', (event) => {
    const form = event.target;

    if (!(form instanceof HTMLFormElement)) return;

    if (
        event.defaultPrevented
        || form.dataset.noNavigationLoader !== undefined
        || (form.target && form.target !== '_self')
    ) {
        return;
    }

    scheduleNavigationLoader();
});

window.addEventListener('pageshow', resetNavigationLoader);
document.addEventListener('turbo:before-visit', scheduleNavigationLoader);
document.addEventListener('turbo:submit-start', scheduleNavigationLoader);
document.addEventListener('turbo:load', resetNavigationLoader);
document.addEventListener('turbo:submit-end', (event) => {
    if (!event.detail.success) resetNavigationLoader();
});

let healthChartInstances = [];
let chartRuntimePromise;

function loadChartRuntime() {
    chartRuntimePromise ??= import('chart.js').then((chartModule) => {
        chartModule.Chart.register(
            chartModule.CategoryScale,
            chartModule.Filler,
            chartModule.Legend,
            chartModule.LinearScale,
            chartModule.LineController,
            chartModule.LineElement,
            chartModule.PointElement,
            chartModule.Tooltip,
        );

        return chartModule.Chart;
    });

    return chartRuntimePromise;
}

function destroyHealthCharts() {
    healthChartInstances.forEach((chart) => chart.destroy());
    healthChartInstances = [];
}

async function initializeHealthCharts() {
    destroyHealthCharts();

    let chartContainers = document.querySelectorAll('[data-health-chart]');

    if (!chartContainers.length) return;

    const Chart = await loadChartRuntime();
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    chartContainers = document.querySelectorAll('[data-health-chart]');

    chartContainers.forEach((container) => {
        const canvas = container.querySelector('canvas');
        const configuration = container.querySelector('[data-chart-config]');

        if (!canvas || !configuration) return;

        const chartData = JSON.parse(configuration.textContent);
        const datasets = chartData.datasets.map((dataset) => ({
            ...dataset,
            borderWidth: 2.5,
            fill: true,
            pointBackgroundColor: '#ffffff',
            pointBorderWidth: 2,
            pointRadius: 3,
            pointHoverRadius: 5,
            spanGaps: true,
            tension: 0.32,
        }));

        healthChartInstances.push(new Chart(canvas, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets,
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: reduceMotion ? false : { duration: 450 },
                interaction: { intersect: false, mode: 'index' },
                plugins: {
                    legend: {
                        display: datasets.length > 1,
                        labels: { boxWidth: 10, boxHeight: 10, usePointStyle: true },
                    },
                    tooltip: { displayColors: datasets.length > 1 },
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#64748b', maxRotation: 0, maxTicksLimit: 6 },
                    },
                    y: {
                        beginAtZero: false,
                        grid: { color: 'rgba(148, 163, 184, 0.18)' },
                        ticks: { color: '#64748b' },
                        title: { display: true, text: chartData.unit, color: '#475569' },
                    },
                },
            },
        }));
    });
}

document.addEventListener('turbo:before-render', destroyHealthCharts);
document.addEventListener('turbo:load', initializeHealthCharts);

if ('serviceWorker' in navigator) {
    window.addEventListener('load', async () => {
        try {
            const serviceWorkerUrl = document.querySelector('meta[name="pwa-service-worker"]')?.content;
            const serviceWorkerScope = document.querySelector('meta[name="pwa-scope"]')?.content;

            if (!serviceWorkerUrl || !serviceWorkerScope) return;

            const registration = await navigator.serviceWorker.register(serviceWorkerUrl, { scope: serviceWorkerScope });

            registration.addEventListener('updatefound', () => {
                const worker = registration.installing;
                worker?.addEventListener('statechange', () => {
                    if (worker.state === 'installed' && navigator.serviceWorker.controller) {
                        showConnectionStatus('An app update is ready. Refresh to use it.', true);
                    }
                });
            });
        } catch (error) {
            console.error('Service worker registration failed.', error);
        }
    });
}
