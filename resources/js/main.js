document.addEventListener('DOMContentLoaded', function () {
    function hasIntro() {
        return typeof introJs !== 'undefined';
    }

    function isOnboardingActive() {
        try {
            const params = new URLSearchParams(window.location.search);
            return params.get('onboarding') === '1';
        } catch (e) {
            return false;
        }
    }

    function withOnboarding(url) {
        const u = new URL(url, window.location.origin);
        u.searchParams.set('onboarding', '1');
        return u.pathname + '?' + u.searchParams.toString();
    }

    function startTour(steps, onCompleteRedirectUrl) {
        if (!hasIntro()) return;

        const tour = introJs();
        tour.setOptions({
            steps: steps,
            showProgress: true,
            scrollToElement: true,
            tooltipClass: 'pp-intro',
            exitOnOverlayClick: false,
            exitOnEsc: false,
            nextLabel: 'Next',
            prevLabel: 'Back',
            doneLabel: 'Next',
        });

        // Hide Back button on the first step only.
        tour.onafterchange(function () {
            const prevBtn = document.querySelector('.introjs-prevbutton');
            if (!prevBtn) return;
            if (tour._currentStep === 0) {
                prevBtn.style.display = 'none';
            } else {
                prevBtn.style.display = '';
            }
        });

        tour.oncomplete(function () {
            if (onCompleteRedirectUrl) {
                window.location.href = onCompleteRedirectUrl;
            }
        });

        // If this run was manually started as a restart, cancelling/closing should
        // return the user to the real dashboard (and remove onboarding params).
        tour.onexit(function () {
            try {
                const params = new URLSearchParams(window.location.search);
                const isRestart = params.get('restart') === '1';
                if (!isRestart) return;

                // Always land on /home with no query params so onboarding won't auto-start again.
                window.location.href = '/home';
            } catch (e) {
                // ignore
            }
        });

        tour.start();
    }

    if (!isOnboardingActive()) {
        return;
    }

    const path = window.location.pathname;

    // 1) Home -> Games
    if (path === '/home') {
        const sidebarGamesLink = document.querySelector('#sidebarGamesLink');
        const steps = [
            { title: 'Welcome to Pickup Puck', intro: 'A quick tour to get you comfortable.' },
            { element: document.querySelector('#gameCard'), title: 'Upcoming games', intro: 'Game cards show the time, status, and price.' },
            { element: document.querySelector('#gameLocation_Players'), title: 'Roster snapshot', intro: 'See the location and how many players/goalies are in.' },
            { element: sidebarGamesLink || document.querySelector('#gameMoreDetails'), title: 'Next stop: Games', intro: 'Use the Games link in the sidebar to browse all games.' },
        ].filter(s => !s.element || s.element);

        startTour(steps, withOnboarding('/games'));
        return;
    }

    // 2) Games -> Profile
    if (path === '/games') {
        const detailsLink = document.querySelector('#onbGameDetailsLink');
        const steps = [
            { title: 'Games list', intro: 'Browse games by season and open details.' },
            { element: detailsLink || document.querySelector('table'), title: 'Open details', intro: 'Use “Details” to view a game, accept, and manage guests.' },
        ].filter(s => !s.element || s.element);

        startTour(steps, withOnboarding('/profile'));
        return;
    }

    // 3) Profile -> Demo Game Details
    if (path === '/profile') {
        const steps = [
            { title: 'Your profile', intro: 'Set your default position and keep your account info current.' },
            { element: document.querySelector('#playerDesiredRole'), title: 'Default position', intro: 'This will pre-select your role when you accept games.' },
            { element: document.querySelector('#updateProfile'), title: 'Update', intro: 'Save changes any time.' },
        ].filter(s => !s.element || s.element);

        startTour(steps, withOnboarding('/onboarding/game-details'));
        return;
    }

    // 4) Demo Game Details -> user clicks Finish button (server marks completion)
    if (path === '/onboarding/game-details') {
        const steps = [
            { title: 'Game details', intro: 'Here’s what you’ll see when you open a game.' },
            { element: document.querySelector('#gameMap'), title: 'Location', intro: 'Quickly open the rink location in Maps.' },
            { element: document.querySelector('#acceptGameDiv'), title: 'Accept the game', intro: 'Pick a role and join the roster.' },
            { element: document.querySelector('#attendingGuestsDiv'), title: 'Bring a guest', intro: 'Add a friend to the roster if needed.' },
            { element: document.querySelector('#onbFinish'), title: 'All set', intro: 'Click Finish to complete onboarding.' },
        ].filter(s => !s.element || s.element);

        // On the last step we let Intro.js close; completion is persisted by clicking the page Finish button.
        if (!hasIntro()) return;
        const tour = introJs();
        tour.setOptions({
            steps: steps,
            showProgress: true,
            scrollToElement: true,
            tooltipClass: 'pp-intro',
            exitOnOverlayClick: false,
            exitOnEsc: false,
            nextLabel: 'Next',
            prevLabel: 'Back',
            doneLabel: 'Close',
        });
        tour.start();
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
