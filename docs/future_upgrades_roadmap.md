# Pickup Puck — Future Upgrades & Feature Roadmap

This document catalogs future enhancements and features identified for the Pickup Puck platform, organized by priority and operational value for league organizers and players.

---

## 1. Financials & Payment Tracking (Organizer Upgrade)

### Overview
Currently, the application tracks `ice_cost`, per-game fee, and collected funds, but the manager still manually tracks who has sent an Interac e-Transfer or paid in cash.

### Planned Capabilities:
- **Per-Player Payment Status:**
  - Admin toggle on the game roster: `Paid` vs. `Pending / Unpaid`.
  - Payment method tagging: `e-Transfer`, `Cash`, `Season Pass`.
- **e-Transfer Instructions Card:**
  - Display organizer's e-Transfer email and auto-generated transfer note (e.g. `Game #12 - John Doe`) directly on the game details card.
- **One-Click Mark All Paid:**
  - For games where regular players pay at the rink or pre-pay via season passes.
- **Financial Summary Report:**
  - Track ice rental costs vs. total revenue collected across the season.

---

## 2. Automated Jersey Color Notifications (T-30 Alerts)

### Overview
When teams are revealed and locked 30 minutes before puck drop, players are often on the road or in the dressing room and don't check their web browser.

### Planned Capabilities:
- **T-30 Email / SMS Notification:**
  - An automated transactional dispatch triggered when teams lock:
    > *"Hey Marcus! Teams are locked for tonight's game at 9:30 PM. You're skating on **Dark**! Bring your dark jersey."*
- **Push Notifications (Web Push / PWA):**
  - Instant mobile notification on player phones without needing SMS carrier charges.
- **Player Notification Preferences:**
  - Allow players to choose email, SMS, or in-app notification only.

---

## 3. Emergency Contact & Safety Cards

### Overview
Hockey is a high-speed collision sport. If a player sustains an injury, takes a puck to the face, or requires medical attention, the organizer needs immediate access to emergency contact details.

### Planned Capabilities:
- **Profile Fields:**
  - Emergency Contact Name
  - Emergency Contact Phone Number
  - Relationship (Spouse, Parent, Friend)
  - Medical Notes (optional: allergies, asthma, etc.)
- **Admin Emergency Sheet:**
  - Secure, admin-only button on the game roster that pulls up the emergency contacts for all attendees checked into that specific game.

---

## 4. Printable Locker Room Roster / Bench Sheet

### Overview
Organizers often like having a paper printout or a clean tablet view to place on the locker room board or bench.

### Planned Capabilities:
- **1-Page Printable Roster:**
  - Clean high-contrast printable view (`@media print`) showing Dark vs. Light rosters, jersey numbers, and goalie assignments.
  - Check-in boxes for attendance confirmation.

---

## 5. Progressive Web App (PWA) & Offline Access

### Overview
Make Pickup Puck installable directly on iOS and Android home screens without going through the App Store.

### Planned Capabilities:
- App icon on phone home screen.
- Full-screen native app feel without browser navigation chrome.
- Instant access to tonight's game time, rink address, and team assignment.
