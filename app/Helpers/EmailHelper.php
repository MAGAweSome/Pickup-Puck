<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class EmailHelper
{
    /**
     * Generate a smart date message for the email body based on game time.
     * - 2+ days away: "on {date}"
     * - 1 day away: "Tomorrow, {date}"
     * - Same day: "Today, {date}"
     */
    public static function getSmartDateMessage(Carbon $gameTime): string
    {
        $now = Carbon::now();
        $daysUntil = $now->diffInDays($gameTime, false);

        $dateFormat = 'l, F j \a\t g:i A'; // "Monday, January 14 at 7:30 PM"
        $formattedDate = $gameTime->format($dateFormat);

        if ($daysUntil > 1) {
            return "on {$formattedDate}";
        } elseif ($daysUntil === 1) {
            return "Tomorrow, {$formattedDate}";
        } else {
            // Same day or in the past
            return "Today, {$formattedDate}";
        }
    }

    /**
     * Generate email body for the upcoming game reminder with hockey flair.
     */
    public static function generateEmailBody(string $gameTitle, string $smartDateMessage): string
    {
        return "🏒 UPCOMING GAME REMINDER 🏒\n\n"
            . "Hey there!\n\n"
            . "Don't miss out! We have an upcoming game:\n\n"
            . "🎯 {$gameTitle}\n"
            . "🕐 {$smartDateMessage}\n\n"
            . "Mark your calendar and get ready to hit the ice! 🥅\n\n"
            . "See you there! 🏒";
    }

    /**
     * Generate a mailto link with populated recipients and email body.
     * Uses %20 encoding for spaces instead of + for better compatibility.
     * 
     * @param Collection $players Collection of players with email addresses
     * @param string $subject Email subject
     * @param string $body Email body
     */
    public static function generateMailtoLink(Collection $players, string $subject, string $body): string
    {
        // Extract email addresses from players
        $emails = $players->pluck('email')->filter()->unique()->values();
        
        if ($emails->isEmpty()) {
            return '';
        }

        $recipientsList = $emails->implode(',');
        
        // Use rawurlencode to produce %20 for spaces instead of +
        $encodedSubject = rawurlencode($subject);
        $encodedBody = rawurlencode($body);

        return "mailto:{$recipientsList}?subject={$encodedSubject}&body={$encodedBody}";
    }
}
