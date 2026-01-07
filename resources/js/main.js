document.addEventListener("DOMContentLoaded", function () {
    // Function to check if the current page is the home page
    function isHomePage() {
        return window.location.pathname === "/home";
    }

    // Function to check if the current page is the game page
    function isGamePage() {
        const re = /game\/\d+/gm;
        return re.test(window.location.pathname);
    }

    // Function to check if the current page is the profile page
    function isProfilePage() {
        return window.location.pathname === "/profile";
    }

    // Function to start the Intro.js tour for the home page
    function startHomeIntroTour() {
        const tour = introJs();
        var gameHref = document.querySelector("#gameMoreDetails").href;

        // Define the tour steps
        let tourSteps = [
            {
                title: "Welcome!",
                intro: "Let me show you how to navigate the site.",
            },
            {
                element: document.querySelector("#gameCard"),
                title: "Games",
                intro: "This is what an upcoming game will look like. It will show you what game it is, when it is, if you are currently attending or not, and will tell you the cost for that game.",
            },
            {
                element: document.querySelector("#gameLocation_Players"),
                title: "Games",
                intro: "Here will show you the location of the game and the current number of players and goalies signed up for the game. A map of the game location will be shown on the next page!",
            },
            {
                element: document.querySelector("#gameMoreDetails"),
                title: "Game Details",
                intro: "Please click here to see more details about the game and to accept the game!",
            },
        ];

        // Attach the oncomplete callback only if the user is seeing the page for the first time
        if (!localStorage.getItem("homeIntroCompleted") && isHomePage()) {
            tour.onbeforeexit(function () {
                if (tour._currentStep < tour._introItems.length - 1) {
                    // Show a message to finish the guide if not the last step
                    alert("Please finish the tour before exiting.");
                    return false; // Prevent the user from skipping the tour
                }
            });

            tour.oncomplete(function () {
                // Save that the home page tour is completed
                localStorage.setItem("homeIntroCompleted", "true");
                // Redirect to the game page and start the game tour
                window.location.href = gameHref;
            });
        }

        // Set the Intro.js options with the specified steps
        tour.setOptions({
            steps: tourSteps,
        });

        // Start the tour
        tour.start();
    }

    // Function to start the Intro.js tour for the game page
    function startGameIntroTour() {
        const tour = introJs();

        // Define the tour steps
        let tourSteps = [
            {
                title: "Game Detail Page",
                intro: "This is the page that will show you more details about a game.",
            },
            {
                element: document.querySelector("#gameMap"),
                title: "Game Location",
                intro: "This is the location of the desired game. You can click and drag to move the map around and resize.",
            },
            {
                element: document.querySelector("#gameDetailTable"),
                title: "This is quick Game Details",
                intro: "You will be able to see the game time, the location address, how long the game is scheduled for, and the price of the game.",
            },
            {
                element: document.querySelector("#acceptGameDiv"),
                title: "Accepting A Game",
                intro: "This will automatically put in your default role for games as set in your profile. If you want to change your position to goalie for one game, you may click the dropdown and change it before you click the Accept Game button.",
            },
            {
                element: document.querySelector("#attendingGuestsDiv"),
                title: "Bringing A Guest",
                intro: "If you want to bring a guest to a game, please enter their full name, select the dropdown to change their role (Default role will be player), and then submit!",
            },
            {
                element: document.querySelector("#gameSkaters"),
                title: "Game Skaters",
                intro: "This will show the current players that are signed up for the game. The players will be on the left and the goalies will be on the right.",
            },
            {
                element: document.querySelector("#gameTeam"),
                title: "Teams",
                intro: "This is where you can see your team for the desired game. The team will only be made 30 minutes before the game.",
            },
            {
                element: document.querySelector("#navigationBar"),
                // element: document.querySelector("#navDropdown"),
                title: "Nav Dropdown",
                intro: "This is where you can view your profile by activating this dropdown and click profile.",
            },
        ];

        // Set the Intro.js options with the specified steps
        tour.setOptions({
            steps: tourSteps,
        });

        // Attach the oncomplete callback only if the user is seeing the page for the first time
        if (!localStorage.getItem("gameIntroCompleted") && isGamePage()) {
            tour.onbeforeexit(function () {
                if (tour._currentStep < tour._introItems.length - 1) {
                    // Show a message to finish the guide if not the last step
                    alert("Please finish the tour before exiting.");
                    return false; // Prevent the user from skipping the tour
                }
            });

            tour.oncomplete(function () {
                // Save that the game page tour is completed
                localStorage.setItem("gameIntroCompleted", "true");
                // Redirect to the profile page and start the profile tour
                window.location.href = "/profile"; // Replace with the actual profile page URL
            });
        }

        // Set the Intro.js options with the specified steps
        tour.setOptions({
            steps: tourSteps,
        });

        // Start the tour
        tour.start();
    }

    // Function to start the Intro.js tour for the profile page
    function startProfileIntroTour() {
        const tour = introJs();

        // Define the tour steps
        let tourSteps = [
            {
                title: "This is your Profile",
                intro: "This is the profile page. Here you can see your information like Name, eMail, and your desired role for games.",
            },
            {
                element: document.querySelector("#playerDesiredRole"),
                title: "Your Desired Game Role",
                intro: "This will show you your desired role for when you accept games. The default is player, but if you would like to accept games as goalie by default this can be changed.",
            },
            {
                element: document.querySelector("#updateProfile"),
                title: "Update Profile",
                intro: "This is where you will go to change any account information or if you want to change your role to be the default goalie. <br><br>Click me if you would like to see the update page!",
            },
        ];

        // Attach the oncomplete callback only if the user is seeing the page for the first time
        if (!localStorage.getItem("profileIntroCompleted") && isProfilePage()) {
            tour.onbeforeexit(function () {
                if (tour._currentStep < tour._introItems.length - 1) {
                    // Show a message to finish the guide if not the last step
                    alert("Please finish the tour before exiting.");
                    return false; // Prevent the user from skipping the tour
                }
            });

            tour.oncomplete(function () {
                // Save that the profile page tour is completed
                localStorage.setItem("profileIntroCompleted", "true");
                // Redirect back to the home page and start the home page tour
                window.location.href = "/home"; // Replace with the actual home page URL
            });
        }

        // Set the Intro.js options with the specified steps
        tour.setOptions({
            steps: tourSteps,
        });

        // Start the tour
        tour.start();
    }

    // Check if the home page tour has already been completed
    if (!localStorage.getItem("homeIntroCompleted") && isHomePage()) {
        startHomeIntroTour();
    }

    // Check if the game page tour has already been completed
    if (!localStorage.getItem("gameIntroCompleted") && isGamePage()) {
        startGameIntroTour();
    }

    // Check if the profile page tour has already been completed
    if (!localStorage.getItem("profileIntroCompleted") && isProfilePage()) {
        startProfileIntroTour();
    }

    // Add an event listener to the button for manual tour triggering (if present on page)
    const startTourButton = document.getElementById("start-tour");
    if (startTourButton) {
        startTourButton.addEventListener("click", function () {
            if (isHomePage()) {
                startHomeIntroTour();
            } else if (isGamePage()) {
                startGameIntroTour();
            } else if (isProfilePage()) {
                startProfileIntroTour();
            }
        });
    }
});

// On the create game blade, this will highlight the season selector if it is not slected.
$(document).ready(function () {
    $('#createGameForm').on('submit', function (e) {
        var selectedSeason = $('#season').val();
        if (selectedSeason === "") {
            e.preventDefault(); // Prevent form submission
            $('#season').css('border-color', '#f00'); // Add a red border
        } else {
            $('#season').css('border-color', ''); // Remove the red border if a valid option is selected
        }
    });
});
