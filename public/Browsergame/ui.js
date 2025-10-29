import * as config from './config.js';
import { formatTime } from './utils.js';

// Get DOM elements (cached for performance)
const timerDisplay = document.getElementById(config.TIMER_ID);
const scoreDisplay = document.getElementById(config.SCORE_ID);
const speechBubbleElement = document.getElementById(config.SPEECH_BUBBLE_ID);
const gameOverScreen = document.getElementById(config.GAME_OVER_SCREEN_ID);
const startScreen = document.getElementById(config.START_SCREEN_ID);
const finalTimeDisplay = document.getElementById(config.FINAL_TIME_ID);
const finalScoreDisplay = document.getElementById(config.FINAL_SCORE_ID);

// Check if elements were found (basic check)
if (!timerDisplay) console.error("UI Error: Timer display not found!");
if (!scoreDisplay) console.error("UI Error: Score display not found!");
if (!speechBubbleElement) console.error("UI Error: Speech bubble not found!");
if (!gameOverScreen) console.error("UI Error: Game Over screen not found!");
if (!startScreen) console.error("UI Error: Start screen not found!");
if (!finalTimeDisplay) console.error("UI Error: Final Time display not found!");
if (!finalScoreDisplay) console.error("UI Error: Final Score display not found!");


let bubbleTimeoutId;

export function updateTimerDisplay(seconds) {
    if (timerDisplay) timerDisplay.innerText = formatTime(seconds);
}

export function updateScoreDisplay(score) {
    if (scoreDisplay) scoreDisplay.innerText = "Score: " + score;
}

export function showSpeechBubble(gameRunningState) {
    if (!gameRunningState || !speechBubbleElement) return;

    // Hole die Daten des aktuell gewählten Charakters
    const charData = config.getSelectedCharacter();
    const bubbleImages = charData.speechBubbles;

    // Zeige nichts an, wenn der Charakter keine Sprechblasen hat
    if (!bubbleImages || bubbleImages.length === 0) {
        return;
    }
    
    const randomIndex = Math.floor(Math.random() * bubbleImages.length);
    speechBubbleElement.style.backgroundImage = `url('${bubbleImages[randomIndex]}')`;
    speechBubbleElement.style.display = "block";

    setTimeout(() => {
        if (speechBubbleElement) speechBubbleElement.style.display = "none";
        if (gameRunningState) {
            startBubbleTimer(gameRunningState);
        }
    }, config.BUBBLE_DISPLAY_DURATION);
}

export function startBubbleTimer(gameRunningState) {
    if (!gameRunningState) return;
    clearTimeout(bubbleTimeoutId);
    const randomTime = Math.random() * config.BUBBLE_MAX_INTERVAL_RANDOM + config.BUBBLE_MIN_INTERVAL;
    bubbleTimeoutId = setTimeout(() => showSpeechBubble(gameRunningState), randomTime);
}

export function stopBubbleTimer() {
    clearTimeout(bubbleTimeoutId);
    if (speechBubbleElement) speechBubbleElement.style.display = "none";
}

export function showStartScreen() {
    if (startScreen) startScreen.style.display = 'flex';
    console.log("UI: Showing Start Screen");
}

export function hideStartScreen() {
    if (startScreen) startScreen.style.display = 'none';
    console.log("UI: Hiding Start Screen");
}

export function isStartScreenVisible() {
    return startScreen && startScreen.style.display === 'flex';
}

export function showGameOverScreen(seconds, score) {
    if (finalTimeDisplay) finalTimeDisplay.innerText = "Riesiger Klumpen Code Einschlagszeit: " + formatTime(seconds);
    if (finalScoreDisplay) finalScoreDisplay.innerText = "Score: " + score;
    if (gameOverScreen) gameOverScreen.style.display = 'flex';
    console.log("UI: Showing Game Over Screen");
}

export function hideGameOverScreen() {
    if (gameOverScreen) gameOverScreen.style.display = 'none';
    console.log("UI: Hiding Game Over Screen");
}

export function isGameOverScreenVisible() {
    return gameOverScreen && gameOverScreen.style.display === 'flex';
}