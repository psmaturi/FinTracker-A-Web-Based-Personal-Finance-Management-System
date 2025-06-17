document.getElementById("goalForm").addEventListener("submit", function (event) {
    event.preventDefault();

    const goalName = document.getElementById("goalName").value;
    const targetAmount = document.getElementById("targetAmount").value;
    const deadline = document.getElementById("deadline").value;

    fetch("add_goal.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ goalName, targetAmount, deadline })
    })
    .then(response => response.json())
    .then(data => {
        alert(data.success || data.error);
        fetchGoals(); // Refresh goals after adding
    })
    .catch(error => console.error("Error:", error));
});

function fetchGoals() {
    fetch("fetch_goals.php")
        .then(response => response.json())
        .then(goals => {
            const goalsList = document.getElementById("goalsList");
            const completedGoals = document.getElementById("completedGoals");
            goalsList.innerHTML = ""; // Clear previous goals
            completedGoals.innerHTML = ""; // Clear completed goals

            goals.forEach(goal => {
                const goalItem = document.createElement("div");
                goalItem.classList.add("goal-item");
                goalItem.innerHTML = `
                    <h3>${goal.goal_name}</h3>
                    <p>Target: $${goal.target_amount}</p>
                    <p>Saved: $${goal.saved_amount}</p>
                    <p>Deadline: ${goal.deadline}</p>
                    <progress value="${goal.saved_amount}" max="${goal.target_amount}"></progress>
                    <input type="number" class="saveAmount" placeholder="Add Savings">
                    <button onclick="updateSavings(${goal.id})">Update</button>
                `;

                if (goal.completed) {
                    completedGoals.appendChild(goalItem);
                } else {
                    goalsList.appendChild(goalItem);
                }
            });
        })
        .catch(error => console.error("Error fetching goals:", error));
}

// Toggle goals visibility when the button is clicked
document.getElementById("toggleGoals").addEventListener("click", function () {
    const goalsList = document.getElementById("goalsList");
    if (goalsList.style.display === "none") {
        goalsList.style.display = "block";
        fetchGoals(); // Load goals only when button is clicked
        this.textContent = "Hide Goals";
    } else {
        goalsList.style.display = "none";
        this.textContent = "View Goals";
    }
});

function updateSavings(goalId) {
    const saveAmount = document.querySelector(`[onclick="updateSavings(${goalId})"]`).previousElementSibling.value;

    fetch("update_savings.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ goalId, savedAmount: saveAmount })
    })
    .then(response => response.json())
    .then(data => {
        alert(data.success || data.error);
        fetchGoals(); // Refresh goals after updating savings
    })
    .catch(error => console.error("Error updating savings:", error));
}
