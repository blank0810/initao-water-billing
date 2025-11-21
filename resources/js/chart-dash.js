import Chart from "chart.js/auto";

// ========= Helper =========
function createGradient(ctx, colorStart, colorEnd) {
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, colorStart);
    gradient.addColorStop(1, colorEnd);
    return gradient;
}

// ========= Global Defaults =========
Chart.defaults.responsive = true;
Chart.defaults.maintainAspectRatio = false;
Chart.defaults.color = "#9ca3af";
Chart.defaults.font.family = "'Inter', sans-serif";

// ========= Revenue Trend (Line Chart) =========
const revenueCtx = document.getElementById("revenueChart");
if (revenueCtx) {
    const gradient = createGradient(revenueCtx.getContext("2d"), "#3b82f6", "#93c5fd");

    new Chart(revenueCtx, {
        type: "line",
        data: {
            labels: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
            datasets: [
                {
                    label: "Revenue (₱)",
                    data: [25000, 30000, 28000, 35000, 40000, 37000, 42000],
                    borderColor: "#2563eb",
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: "#1d4ed8",
                },
            ],
        },
        options: {
            layout: {
                padding: { left: 10, right: 10, top: 10, bottom: 10 },
            },
            plugins: {
                legend: { display: false },
                title: { display: false },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { color: "#9ca3af" },
                    grid: { color: "rgba(156, 163, 175, 0.2)" },
                },
                x: {
                    ticks: { color: "#9ca3af" },
                    grid: { display: false },
                },
            },
        },
    });
}

// ========= Customer Distribution (Doughnut, Legend Right) =========
const customerDistCtx = document.getElementById("customerDistributionChart");
if (customerDistCtx) {
    new Chart(customerDistCtx, {
        type: "doughnut",
        data: {
            labels: ["Residential", "Commercial", "Industrial", "Government"],
            datasets: [
                {
                    label: "Customers",
                    data: [1500, 800, 300, 247],
                    backgroundColor: ["#3b82f6", "#10b981", "#f59e0b", "#ef4444"],
                    hoverOffset: 10,
                },
            ],
        },
        options: {
            layout: { padding: 20 },
            plugins: {
                legend: {
                    position: "right",
                    align: "center",
                    labels: {
                        color: "#9ca3af",
                        boxWidth: 15,
                        padding: 15,
                    },
                },
            },
        },
    });
}

// ========= Consumption Analytics (Bar Chart, Full Fit) =========
const consumptionCtx = document.getElementById("consumptionChart");
if (consumptionCtx) {
    new Chart(consumptionCtx, {
        type: "bar",
        data: {
            labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct"],
            datasets: [
                {
                    label: "Total Consumption (m³)",
                    data: [2000, 2500, 2200, 2700, 3000, 2800, 3200, 3100, 3500, 3700],
                    backgroundColor: "#3b82f6",
                    borderRadius: 6,
                    barThickness: "flex",
                    maxBarThickness: 40,
                },
            ],
        },
        options: {
            layout: { padding: 10 },
            plugins: {
                legend: { display: false },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { color: "#9ca3af" },
                    grid: { color: "rgba(156, 163, 175, 0.2)" },
                },
                x: {
                    ticks: { color: "#9ca3af" },
                    grid: { display: false },
                },
            },
        },
    });
}

// ========= Payment Status (Pie Chart, Legend Right) =========
const paymentCtx = document.getElementById("paymentStatusChart");
if (paymentCtx) {
    new Chart(paymentCtx, {
        type: "pie",
        data: {
            labels: ["Paid", "Pending", "Overdue"],
            datasets: [
                {
                    label: "Payments",
                    data: [2800, 250, 197],
                    backgroundColor: ["#10b981", "#f59e0b", "#ef4444"],
                },
            ],
        },
        options: {
            layout: { padding: 20 },
            plugins: {
                legend: {
                    position: "right",
                    align: "center",
                    labels: {
                        color: "#9ca3af",
                        boxWidth: 15,
                        padding: 15,
                    },
                },
            },
        },
    });
}

// ========= Meter Types (Bar Chart) =========
const meterCtx = document.getElementById("meterTypesChart");
if (meterCtx) {
    new Chart(meterCtx, {
        type: "bar",
        data: {
            labels: ["Analog", "Digital", "Smart"],
            datasets: [
                {
                    label: "Meters Installed",
                    data: [1500, 700, 343],
                    backgroundColor: ["#60a5fa", "#34d399", "#f87171"],
                    borderRadius: 6,
                    barThickness: "flex",
                    maxBarThickness: 50,
                },
            ],
        },
        options: {
            layout: { padding: 10 },
            plugins: {
                legend: {
                    position: "bottom",
                    labels: { color: "#9ca3af" },
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { color: "#9ca3af" },
                    grid: { color: "rgba(156, 163, 175, 0.2)" },
                },
                x: {
                    ticks: { color: "#9ca3af" },
                    grid: { display: false },
                },
            },
        },
    });
}
