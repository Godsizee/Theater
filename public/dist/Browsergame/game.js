import * as config from './config.js';
import * as ui from './ui.js';
import { Character } from './character.js';
import { Enemy } from './enemy.js';

// Game state variables
let gameRunning = false;
let score = 0;
let seconds = 0;
let scoreMultiplier = 1;
let backgroundPosition = 0;

let mainIntervalId;
let gameLoopAnimationId;
let backgroundAnimationId;

// WICHTIG: Deklariere die Variablen hier, aber erstelle die Objekte erst in init()
let player;
let enemyInstance;

function startGame() {
    console.log("startGame called");
    try {
        score = 0;
        seconds = 0;
        scoreMultiplier = 1;
        backgroundPosition = 0;
        document.body.style.backgroundPosition = backgroundPosition + "px 0";

        player.reset();
        enemyInstance.resetPosition();
        enemyInstance.resetSpeed();

        ui.updateScoreDisplay(score);
        ui.updateTimerDisplay(seconds);
        ui.hideStartScreen();
        
        gameRunning = true;
        console.log("gameRunning set to true");

        ui.startBubbleTimer(gameRunning);

        clearInterval(mainIntervalId);
        mainIntervalId = setInterval(() => {
            if (!gameRunning) return;
            seconds++;
            score += scoreMultiplier;
            ui.updateTimerDisplay(seconds);
            ui.updateScoreDisplay(score);

            if (seconds > 0 && seconds % config.SCORE_MULTIPLIER_INCREASE_INTERVAL === 0) {
                scoreMultiplier++;
            }
            if (seconds > 0 && seconds % config.ENEMY_SPEED_INCREASE_INTERVAL === 0) {
                enemyInstance.increaseSpeed();
            }
        }, 1000);
        
        cancelAnimationFrame(gameLoopAnimationId);
        cancelAnimationFrame(backgroundAnimationId);

        gameLoopAnimationId = requestAnimationFrame(() => {
            gameLoopAnimationId = requestAnimationFrame(gameLoop);
            backgroundAnimationId = requestAnimationFrame(moveBackgroundLoop);
        });

    } catch (error) {
        console.error("Error in startGame:", error);
        gameRunning = false;
    }
}

function gameOver() {
    console.log("gameOver called");
    gameRunning = false;
    clearInterval(mainIntervalId);
    if (gameLoopAnimationId) cancelAnimationFrame(gameLoopAnimationId);
    if (backgroundAnimationId) cancelAnimationFrame(backgroundAnimationId);
    gameLoopAnimationId = null;
    backgroundAnimationId = null;
    ui.stopBubbleTimer();
    ui.showGameOverScreen(seconds, score);
}

function gameLoop() {
    if (!gameRunning) return;
    
    player.update();
    enemyInstance.move();

    if (checkCollision(player, enemyInstance)) {
        console.log("Collision detected!");
        gameOver();
        return;
    }

    gameLoopAnimationId = requestAnimationFrame(gameLoop);
}

function moveBackgroundLoop() {
    if (!gameRunning) return;
    backgroundPosition -= enemyInstance.speed * 0.5; 
    document.body.style.backgroundPosition = backgroundPosition + "px 0";
    backgroundAnimationId = requestAnimationFrame(moveBackgroundLoop);
}

function checkCollision(character, enemy) {
    const charRect = character.element.getBoundingClientRect();
    const enemyRect = enemy.element.getBoundingClientRect();
    const charPadding = config.HITBOX_PADDING.character[character.state];
    const enemyPadding = config.HITBOX_PADDING.enemy;

    const charHitbox = {
        left: charRect.left + charPadding.left,
        right: charRect.right - charPadding.right,
        top: charRect.top + charPadding.top,
        bottom: charRect.bottom - charPadding.bottom
    };

    const enemyHitbox = {
        left: enemyRect.left + enemyPadding.left,
        right: enemyRect.right - enemyPadding.right,
        top: enemyRect.top + enemyPadding.top,
        bottom: enemyRect.bottom - enemyPadding.bottom
    };

    return (
        charHitbox.left < enemyHitbox.right &&
        charHitbox.right > enemyHitbox.left &&
        charHitbox.top < enemyHitbox.bottom &&
        charHitbox.bottom > enemyHitbox.top
    );
}

// ## HAUPT-INITIALISIERUNGSFUNKTION ##
function init() {
    // ## KORREKTUR: Erstelle die Objekte erst, wenn das DOM garantiert bereit ist ##
    player = new Character();
    enemyInstance = new Enemy();

    const characterSelectionContainer = document.getElementById('characterSelection');
    const restartBtn = document.getElementById(config.RESTART_BTN_ID);

    if (characterSelectionContainer) {
        characterSelectionContainer.addEventListener('click', (e) => {
            if (e.target.classList.contains('select-btn') && !e.target.disabled) {
                const selectedKey = e.target.dataset.character;
                config.setSelectedCharacter(selectedKey);
                document.querySelectorAll('.character-card').forEach(card => card.classList.remove('selected'));
                document.getElementById(`card-${selectedKey}`).classList.add('selected');
                console.log(`Character ${selectedKey} selected. Starting game!`);
                startGame();
            }
        });
    } else {
        console.error("Fatal Error: Character selection container not found.");
    }

    if (restartBtn) {
        restartBtn.addEventListener('click', () => {
            console.log("Restart Button clicked!");
            ui.showStartScreen();
            ui.hideGameOverScreen();
        });
    } else {
        console.error("Fatal Error: Restart button not found.");
    }

    document.addEventListener('keydown', (e) => {
        if (ui.isStartScreenVisible() || ui.isGameOverScreenVisible()) {
            return;
        }
        if (gameRunning) {
            if (e.code === config.JUMP_KEY) {
                player.jump();
                e.preventDefault(); 
            } else if (e.code === config.DUCK_KEY) {
                player.duck();
            }
        }
    });

    console.log("Initializing UI: Showing Start Screen");
    ui.showStartScreen();
}

// Event-Listener, der die init-Funktion ausführt, sobald das DOM bereit ist.
document.addEventListener('DOMContentLoaded', init);