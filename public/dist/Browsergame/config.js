// Character Data
export const CHARACTERS = {
  koehnel: {
    name: 'Köhnel',
    avatar: 'character1/Avatar.png',
    enemy: 'character1/Kothaufen.png',
    speechBubbles: [
        "character1/Sprechblasen/bubble1.png",
        "character1/Sprechblasen/bubble2.png",
        "character1/Sprechblasen/bubble3.png",
        "character1/Sprechblasen/bubble4.png",
        "character1/Sprechblasen/bubble5.png"
    ]
  },
  ray: {
    name: 'Ray Nalpatamkalom',
    avatar: 'character2/Avatar.png',
    enemy: 'character2/swt_klausur.png',
    speechBubbles: [] // Leeres Array sorgt dafür, dass keine Sprechblasen angezeigt werden
  }
};

let selectedCharacterKey = 'koehnel'; // Standardauswahl

export function setSelectedCharacter(key) {
  if (CHARACTERS[key]) {
    selectedCharacterKey = key;
  }
}

export function getSelectedCharacter() {
  return CHARACTERS[selectedCharacterKey];
}

// DOM Element IDs
export const CHARACTER_ID = 'character';
export const ENEMY_ID = 'enemy';
export const TIMER_ID = 'timer';
export const SCORE_ID = 'score';
export const SPEECH_BUBBLE_ID = 'speechBubble';
export const GAME_OVER_SCREEN_ID = 'gameOverScreen';
export const RESTART_BTN_ID = 'restartBtn';
export const START_SCREEN_ID = 'startScreen';
export const START_BTN_ID = 'startBtn';
export const FINAL_TIME_ID = 'finalTime';
export const FINAL_SCORE_ID = 'finalScore';

// Game settings
export const INITIAL_ENEMY_SPEED = 10;
export const ENEMY_SPEED_INCREASE_FACTOR = 1.2;
export const SCORE_MULTIPLIER_INCREASE_INTERVAL = 15; // in seconds
export const ENEMY_SPEED_INCREASE_INTERVAL = 20; // in seconds
export const BACKGROUND_MOVE_INTERVAL = 10; // ms, for background animation speed
export const GAME_LOOP_INTERVAL = 10; // ms, for enemy movement and collision checks

// Character settings
export const CHARACTER_NORMAL_HEIGHT = "400px";
export const DUCK_DURATION = 700; // Wie lange der Charakter geduckt bleibt

// --- PHYSIK-EINSTELLUNGEN ---
export const GRAVITY = 0.8;
export const JUMP_STRENGTH = 28;
export const CHARACTER_GROUND_Y = 350; // Y-Position am Boden (als Zahl)
export const CHARACTER_DUCK_Y = 580;   // Y-Position beim Ducken (als Zahl)
export const DUCK_ANIMATION_SPEED = 20; // Geschwindigkeit der Duck-Animation

// Hitbox settings
export const HITBOX_PADDING = {
    character: {
        RUNNING: { top: 40, bottom: 10, left: 130, right: 130 },
        JUMPING: { top: 40, bottom: 10, left: 130, right: 130 },
        DUCKING: { top: 80, bottom: 10, left: 110, right: 110 },
        DUCKING_DOWN: { top: 40, bottom: 10, left: 130, right: 130 },
        DUCKING_UP:   { top: 40, bottom: 10, left: 130, right: 130 }
    },
    enemy: {
        top: 20,
        bottom: 20,
        left: 20,
        right: 30
    }
};

// Enemy settings
export const ENEMY_NORMAL_POS_TOP = "450px";
export const ENEMY_VERY_HIGH_POS_TOP = "180px"; // For ducking under

// Speech Bubble
export const BUBBLE_DISPLAY_DURATION = 3000; // ms
export const BUBBLE_MIN_INTERVAL = 1000; // ms
export const BUBBLE_MAX_INTERVAL_RANDOM = 15000; // ms

// Collision
export const COLLISION_RANGE_X_MIN = -150; // Relative to character
export const COLLISION_RANGE_X_MAX = 150;  // Relative to character

// Keyboard Keys
export const JUMP_KEY = 'Space';
export const DUCK_KEY = 'KeyD';