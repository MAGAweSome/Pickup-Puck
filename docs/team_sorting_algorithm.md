# Pickup Puck — Team Sorting Algorithm Architecture

This document outlines the mechanics of the team sorting and balancing engine in Pickup Puck, how it was overhauled from the legacy system, how it guarantees both competitive fairness and week-to-week lineup variety, and the planned roadmap for future algorithmic enhancements.

---

## 1. Background: The Legacy Algorithm & Why It Failed

The original sorting system used a simple shuffle-and-check approach:
1. Collect all attending skaters.
2. Randomly shuffle the list up to 21 times.
3. Split the shuffled list in half.
4. Sum the `level` (1–5) on each team.
5. If the difference was $\le 2$ (`TEAM_SCORE_DIFF_MAX`), stop immediately and save.

### Fundamental Flaws of the Legacy Approach:
- **The "Ringer" Imbalance:**
  Because the old code only evaluated the *aggregate sum* of player levels, it regularly created blowout matchups. For example, in a 12-skater game with two Level 5 lifelong hockey players, two Level 1 beginners, and eight Level 3 intermediates:
  - **Team 1:** [5, 5, 1, 1, 3, 3] $\rightarrow$ Total Skill: **18**
  - **Team 2:** [3, 3, 3, 3, 3, 3] $\rightarrow$ Total Skill: **18**
  The mathematical skill difference was **0**, so the old system considered this a "perfect" split. However, on the ice, Team 1 completely dominated because two elite Level 5 skaters could control the puck the entire game and run up the score.
- **Inadequate Search Space (Only 21 Samples):**
  For an 18-player game, there are $\binom{18}{9} = 48,620$ possible team combinations; for 24 players, there are $2,704,156$. Sampling only 21 random combinations explored less than 0.04% of possibilities, frequently settling for uneven teams.
- **Goalie Skills Ignored:**
  Goalies were distributed randomly across Dark and Light with zero impact on skater team targets. If one team drew an ex-junior/college goalie (Level 5) and the other drew a novice goalie (Level 2), the game was decided before the puck dropped.
- **Bench Fatigue on Odd Rosters:**
  When 11, 13, 15, or 17 skaters showed up, the team with fewer skaters had a shorter bench and less rest. The legacy system did not account for bench depth vs. individual talent.

---

## 2. The Overhauled Sorting System (How It Works Now)

The new engine in [`app/Services/GameTeamsService.php`](file:///d:/xampp/htdocs/Pickup-Puck/app/Services/GameTeamsService.php) implements a **Multi-Tier Snake Draft with Random Coin Flips & Combinatorial Swap Optimization**:

```
                         All Attending Players
                                  │
          ┌───────────────────────┼───────────────────────┐
          ▼                       ▼                       ▼
    Elite Tier (5 & 4)      Mid Tier (3)         Developing Tier (2 & 1)
          │                       │                       │
     Randomize               Randomize               Randomize
          │                       │                       │
     Pair Players            Pair Players            Pair Players
          │                       │                       │
    50/50 Coin Flip         50/50 Coin Flip         50/50 Coin Flip
    (Dark vs Light)         (Dark vs Light)         (Dark vs Light)
          └───────────────────────┬───────────────────────┘
                                  ▼
                     Initial Seeded Rosters
                                  │
                                  ▼
             Goalie Skill Offset Calculation (Level Delta)
                                  │
                                  ▼
           Greedy Local Swap Optimizer (60 Seeded Iterations)
      Minimizing: Skill Delta + Count Delta + Tier Balance Delta
                                  │
                                  ▼
                   Final Locked Balanced Rosters
```

### Key Mechanics:

### A. Automatic Skill Tiering
Players are split into three distinct competitive buckets:
- **Tier 1 (Elite / Advanced):** Levels 5 & 4
- **Tier 2 (Intermediate):** Level 3
- **Tier 3 (Novice / Developing):** Levels 2 & 1

### B. Tiered 50/50 Coin-Flip Pairing
- Within each tier, players are shuffled and paired up.
- For each pair, a 50/50 coin flip assigns one player to Dark and one to Light.
- **Why this guarantees fairness:** Top-tier players are **guaranteed to be split evenly** across both teams (e.g., 2 elite on Dark, 2 on Light). Beginners are also guaranteed to be split evenly.
- **Why this guarantees weekly variety:** Because the pairing and coin flips are randomized on every run, the probability of drawing the exact same roster twice in a 16-player game is less than $1 \text{ in } 256$ ($2^{8}$).

### C. Goalie Skill Offset Compensation
Goalies are evaluated by their individual skill ratings:
- If Team 1 has a significantly stronger goalie than Team 2 (e.g., Goalie 1 is Level 5, Goalie 2 is Level 2; $\Delta \ge 2$), the target skater skill for Team 2 is automatically adjusted upward by $+1$.
- This provides the team facing the stronger goalie with a slight offensive edge, naturally balancing the scoreboard.

### D. Multi-Objective Combinatorial Swap Optimization
The algorithm runs **60 seeded draft rounds**. In each round, it executes greedy 1-for-1 player swaps between players of identical or adjacent levels to minimize the following penalty formula:

$$\text{Penalty} = 12 \cdot |\Delta\text{SkaterSkill} - \text{GoalieOffset}| + 50 \cdot |\Delta\text{Count}| + 20 \cdot |\Delta\text{Elite}| + 10 \cdot |\Delta\text{Novice}|$$

- **Count Difference:** Always $\le 1$ (0 when even, 1 when odd).
- **Elite Difference:** Always $\le 1$.
- **Novice Difference:** Always $\le 1$.
- **Overall Skill Difference:** Always $\le 1$ point (frequently 0).

---

## 3. Future Enhancements & Algorithmic Roadmap

While the current engine produces balanced and varied rosters, the following upgrades can make the team generation even smarter:

### 1. Forward vs. Defense Positional Balancing (Highest Value)
- **Concept:** Allow players to set a preferred position on their profile: `Forward`, `Defense`, or `Either / Rover`.
- **Why it helps:** In pickup hockey, team balance isn't just numbers—it's structural. If Team 1 has eight natural forwards and two defensemen, while Team 2 has six natural defensemen, players are forced out of position and passing lanes collapse.
- **Implementation:** The balancer will pair players within **both** position and skill tier (e.g., pair the two best defensemen, pair the two best forwards) to ensure a standard 3F : 2D line ratio on both benches.

### 2. Historical Roster Tracking (Anti-Repetition Memory)
- **Concept:** When generating teams, query the previous 1–3 completed games from `game_teams_players`.
- **Why it helps:** Prevents two specific players from playing on the same team three weeks in a row, maximizing league camaraderie and ensuring everyone plays with and against everyone else.
- **Implementation:** Add a small penalty to the scoring function for each pair of players who were teammates in the previous game:
  $$\text{Repetition Penalty} = \sum \text{SharedGamesInLast3Weeks}(p_i, p_j) \times 5$$

### 3. Age & Mobility Pace Adjustment
- **Concept:** Factor in player age and mobility (`users.mobility` 1–5).
- **Why it helps:** A 62-year-old former college player may have Level 4 hockey sense and hands, but Level 2 foot speed and stamina compared to a 22-year-old.
- **Implementation:** Calculate an **Effective Skater Pace Index**:
  $$\text{Pace Index} = (\text{Level} \times 0.70) + (\text{Mobility} \times 0.30)$$
  This ensures neither team gets overburdened with tired legs in the 3rd period.

### 4. Player Chemistry & Carpool Pairs
- **Concept:** Allow admins (or players) to flag "must-play-together" (e.g. father/son, carpoolers) or "must-play-against" pairs.
- **Implementation:** The algorithm treats paired players as a single linked unit during the tier assignment.

### 5. Running Plus/Minus Calibration (Auto-Tuning Levels)
- **Concept:** Over the course of the season, track game scores (Dark vs Light) and which team each player was on.
- **Why it helps:** If Player A has a +35 goal differential over 10 games while listed as Level 3, the system can suggest to the admin that Player A should be upgraded to Level 4.
