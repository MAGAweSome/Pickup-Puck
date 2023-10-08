// Add intro.js

// make sure all cookies are set to the home directory ; path=/ and that debugging is used through console.log()

// Set an array for the needed pages
var pagesArray = ["Home", "Game", "Profile"];
var currentPage = "";

// Set the regex variable
const re = /game\/\d+/gm;

// Store the active page url in a variable
let currentUrl = window.location.href;

// Split and store the active page
var currentUrlSplit = currentUrl.split("/");
var currentUrlLastWord = currentUrlSplit[currentUrlSplit.length - 1];

// Set the initieal value of the cookie
var cookieValue = "";

// Set the variable for intro.js
const intro = introJs();

// This will remove the desired cookie
// document.cookie = "userFirstTimeHome=; expires=Thu, 01 Jan 1970 00:00:00 UTC;";
// document.cookie = "userFirstTimeGame=; expires=Thu, 01 Jan 1970 00:00:00 UTC;";
// document.cookie = "userFirstTimeProfile=; expires=Thu, 01 Jan 1970 00:00:00 UTC;";

// This will add the variables and store them in the cookies
pagesArray.forEach((item) => {
    if (!document.cookie.includes("userFirstTime" + item)) {
        document.cookie =
            "userFirstTime" +
            item +
            "=true; expires=Fri, 31 Dec 9999 12:00:00 UTC; path=/";
    }
});

//document.cookie = newCookie;

// set the cookie based on the page were on
if (re.exec(currentUrl)) {
    // console.log('Im on the game page');

    // This will find the value of the cookie with the name: ...
    cookieValue = document.cookie
        .split("; ")
        .find((row) => row.startsWith("userFirstTimeGame="))
        ?.split("=")[1];
    // console.log("Game: " + cookieValue);

    if (cookieValue == "true") {
        // Run the intro Manually
        intro.setOptions({
            // doneLabel: 'Next Page',
            // tooltipPosition : 'top',
            steps: [
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
                    intro: "You will be able to see the game time, the location address, how long the game is sceduled for, and the price of the game.",
                },
                {
                    element: document.querySelector("#acceptGameDiv"),
                    title: "Accepting A Game",
                    intro: "This will automatically put in your default role for games as set in your profile. If you want to change your position to goalie for one game, you may click the dropdown and change it before you click the Accept Game button.",
                },
                {
                    element: document.querySelector("#attendingGuestsDiv"),
                    title: "Bringing A Guest",
                    intro: "If you want to bring a guest to a game, please enter their Full name, select thr dropdown to change their role (Default role will be player) and then submit!",
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
                    element: document.querySelector("#navDropdown"),
                    title: "Nav Dropdown",
                    intro: "This is where you can view your profile by activating this dropdown and click profile.",
                    // },
                    // {
                    //     element: document.querySelector('#navDropdownProfile'),
                    //     title: 'Profile',
                    //     intro: 'This is where you can view your profile by clicking profile.'
                },
            ],
        });

        intro.oncomplete(function () {
            window.open("/profile", "_self");
        });

        intro.start();

        document.cookie =
            "userFirstTimeGame=false; expires=Fri, 31 Dec 9999 12:00:00 UT; path=/";
    }
} else if (
    pagesArray.includes(
        currentUrlLastWord.charAt(0).toUpperCase() +
            currentUrlLastWord.substr(1).toLowerCase()
    )
) {
    // alert('Im on the ' + currentUrlLastWord + ' page');
    // This will find the value of the cookie with the name: ...
    cookieValue = document.cookie
        .split("; ")
        .find((row) =>
            row.startsWith(
                "userFirstTime" +
                    currentUrlLastWord.charAt(0).toUpperCase() +
                    currentUrlLastWord.substr(1).toLowerCase() +
                    "="
            )
        )
        ?.split("=")[1];
    // console.log(
    //     currentUrlLastWord.charAt(0).toUpperCase() +
    //         currentUrlLastWord.substr(1).toLowerCase() +
    //         ": " +
    //         cookieValue
    // );

    if (cookieValue == "true") {
        runIntro();
        document.cookie =
            "userFirstTime" +
            currentUrlLastWord.charAt(0).toUpperCase() +
            currentUrlLastWord.substr(1).toLowerCase() +
            "=false; expires=Fri, 31 Dec 9999 12:00:00 UT; path=/";
    }
}

// If the value of the cookie is true / This means its the users first time visiting / then set to false
// if (cookieValue == 'true') {
//     document.cookie = "userFirstTime" + item + "=false; expires=Fri, 31 Dec 9999 12:00:00 UT";
// }

// allCookies will get a list of all the cookies on the site
var allCookies = document.cookie;
// console.log(allCookies);

console.log("start haha");

window.runIntro = function () {
    // const intro = introJs();

    if (currentUrl.includes("home")) {
        var gameHref = document.querySelector("#gameMoreDetails").href;

        // alert('You\'re on the Home page: ' + currentUrl);
        intro.setOptions({
            // doneLabel: 'Next Page',
            steps: [
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
                    intro: "Here will show you the location of the game and whe current number of players and goalies signed up for the game. A map of the game location will be shown on the next page!",
                },
                {
                    element: document.querySelector("#gameMoreDetails"),
                    title: "Game Details",
                    intro: "Please click here to see more details about the game and to accept the game!",
                },
            ],
        });

        intro.oncomplete(function () {
            window.open(gameHref, "_self");
        });
    } else if (re.exec(currentUrl)) {
        // alert('You\'re on a Game page: ' + currentUrl);
        intro.setOptions({
            // doneLabel: 'Next Page',
            // tooltipPosition : 'top',
            steps: [
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
                    intro: "You will be able to see the game time, the location address, how long the game is sceduled for, and the price of the game.",
                },
                {
                    element: document.querySelector("#acceptGameDiv"),
                    title: "Accepting A Game",
                    intro: "This will automatically put in your default role for games as set in your profile. If you want to change your position to goalie for one game, you may click the dropdown and change it before you click the Accept Game button.",
                },
                {
                    element: document.querySelector("#attendingGuestsDiv"),
                    title: "Bringing A Guest",
                    intro: "If you want to bring a guest to a game, please enter their Full name, select thr dropdown to change their role (Default role will be player) and then submit!",
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
                    element: document.querySelector("#navDropdown"),
                    title: "Nav Dropdown",
                    intro: "This is where you can view your profile by activating this dropdown and click profile.",
                    // },
                    // {
                    //     element: document.querySelector('#navDropdownProfile'),
                    //     title: 'Profile',
                    //     intro: 'This is where you can view your profile by clicking profile.'
                },
            ],
        });

        intro.oncomplete(function () {
            window.open("/profile", "_self");
        });
    } else if (currentUrl.includes("profile")) {
        // alert('You\'re on the Profile page: ' + currentUrl);
        intro.setOptions({
            // doneLabel: 'Next Page',
            steps: [
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
                    intro: "This is where you will go to change any account information or if you want to change your role to be default goalie. <br><br>Click me if you would like to see the update page!",
                },
            ],
        });

        intro.oncomplete(function () {
            window.open("/", "_self");
        });
    }

    intro.start();
};
