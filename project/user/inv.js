let investments = [];
let investmentChart;
let investmentHistory = {
  labels: [],
  datasets: [
    {
      label: "Invested Amount",
      data: [],
      borderColor: "rgba(255, 99, 132, 1)",
      backgroundColor: "rgba(255, 99, 132, 0.2)",
    },
    {
      label: "Profit/Loss",
      data: [],
      borderColor: "rgba(54, 162, 235, 1)",
      backgroundColor: "rgba(54, 162, 235, 0.2)",
    },
  ],
};

// Check if user is logged in
function checkSession() {
  fetch('check_session.php')
      .then(response => response.json())
      .then(data => {
          if (!data.loggedIn) {
              window.location.href = 'login.html';
          }
      });
}

// Call this on page load
window.onload = function() {
  checkSession();
  fetchInvestments();
};

// Add this to your existing event handlers
document.getElementById("addInvestmentForm").addEventListener("submit", (e) => {
  e.preventDefault();

  const investmentType = document.getElementById("investmentType").value;
  const investmentName = document.getElementById("investmentName").value;
  const investmentAmount = parseFloat(document.getElementById("investmentAmount").value);
  const currentValue = investmentAmount;

  if (investmentType === "select" || !investmentName || isNaN(investmentAmount) || investmentAmount <= 0) {
      alert("Please fill in all fields correctly.");
      return;
  }

  fetch("add_investments.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
        type: investmentType,
        name: investmentName,
        amount: investmentAmount,
        currentValue: currentValue
    }),
  })
  .then(res => res.json())
  .then(data => {
      if (data.success) {
          fetchInvestments();
          document.getElementById("addInvestmentForm").reset();
      } else {
          alert(data.error || "Error adding investment.");
      }
  })
  .catch(error => {
      console.error("Error:", error);
      alert("Failed to send data to the server.");
  });
});


function fetchInvestments() {
  fetch("fetch_investment.php")
    .then(res => res.json())
    .then(data => {
      investments = data.map(inv => ({
        type: inv.type,
        name: inv.name,
        amount: parseFloat(inv.amount),
        currentValue: parseFloat(inv.current_value)
      }));
      updateTable();
      updateChart();
    });
}

function updateTable() {
  const body = document.getElementById("investmentsBody");
  body.innerHTML = "";

  investments.forEach((inv, i) => {
    const row = document.createElement("tr");
    row.innerHTML = `
      <td>${inv.type}</td>
      <td>${inv.name}</td>
      <td>$${inv.amount.toFixed(2)}</td>
      <td>$${inv.currentValue.toFixed(2)}</td>
      <td><button class="btn btn-danger" onclick="deleteInvestment(${i})">Delete</button></td>
    `;
    body.appendChild(row);
  });

  const total = investments.reduce((sum, inv) => sum + inv.currentValue, 0);
  document.getElementById("portfolioPerformance").innerText = `Total Portfolio Value: $${total.toFixed(2)}`;
}

function updateChart() {
  const ctx = document.getElementById("investmentChart").getContext("2d");
  if (investmentChart) investmentChart.destroy();

  const labels = investments.map((inv, i) => `#${i + 1}`);
  const invested = investments.map(inv => inv.amount);
  const values = investments.map(inv => inv.currentValue - inv.amount);

  investmentChart = new Chart(ctx, {
    type: "line",
    data: {
      labels,
      datasets: [
        { label: "Invested", data: invested, borderColor: "green", backgroundColor: "lightgreen" },
        { label: "Profit/Loss", data: values, borderColor: "red", backgroundColor: "pink" }
      ]
    }
  });
}

function deleteInvestment(index) {
  fetch("delete_investment.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ name: investments[index].name })
  })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        fetchInvestments();
      } else {
        alert("Failed to delete.");
      }
    });
}

window.onload = fetchInvestments;