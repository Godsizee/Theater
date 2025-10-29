import * as config from './config.js';

export class Character {
    constructor() {
        this.element = document.getElementById(config.CHARACTER_ID);
        
        // Physik- und Zustandseigenschaften
        this.y_position = config.CHARACTER_GROUND_Y;
        this.y_velocity = 0;
        // Die Zustände steuern die gesamte Logik
        this.state = 'RUNNING'; 

        this.reset();
    }

    reset() {
        const charData = config.getSelectedCharacter();
        this.element.style.backgroundImage = `url('${charData.avatar}')`;
        
        this.state = 'RUNNING';
        this.y_position = config.CHARACTER_GROUND_Y;
        this.y_velocity = 0;

        this.element.style.top = this.y_position + 'px';
        this.element.style.height = config.CHARACTER_NORMAL_HEIGHT;
        this.element.style.transition = 'none';
    }

    /**
     * Die Update-Funktion, die in jedem Frame aus der gameLoop aufgerufen wird.
     */
    update() {
        // 1. Logik für den Sprung
        if (this.state === 'JUMPING') {
            this.y_velocity += config.GRAVITY;
            this.y_position += this.y_velocity;

            if (this.y_position >= config.CHARACTER_GROUND_Y) {
                this.y_position = config.CHARACTER_GROUND_Y;
                this.y_velocity = 0;
                this.state = 'RUNNING';
            }
        } 
        // 2. Logik für die Duck-Animation nach unten
        else if (this.state === 'DUCKING_DOWN') {
            this.y_position += config.DUCK_ANIMATION_SPEED;
            if (this.y_position >= config.CHARACTER_DUCK_Y) {
                this.y_position = config.CHARACTER_DUCK_Y;
                this.state = 'DUCKING';
                // Nachdem die unterste Position erreicht ist, warte für DUCK_DURATION
                setTimeout(() => {
                    // Nur den Zustand ändern, wenn wir noch ducken (und nicht z.B. schon "Game Over" ist)
                    if (this.state === 'DUCKING') {
                        this.state = 'DUCKING_UP';
                    }
                }, config.DUCK_DURATION);
            }
        }
        // 3. Logik für die Duck-Animation nach oben
        else if (this.state === 'DUCKING_UP') {
            this.y_position -= config.DUCK_ANIMATION_SPEED;
            if (this.y_position <= config.CHARACTER_GROUND_Y) {
                this.y_position = config.CHARACTER_GROUND_Y;
                this.state = 'RUNNING';
            }
        }

        // Aktualisiere die visuelle Position des Elements im DOM
        this.element.style.top = this.y_position + 'px';
    }

    /**
     * Startet den Sprung, wenn der Charakter dazu in der Lage ist.
     */
    jump() {
        if (this.state !== 'RUNNING') return;
        
        this.state = 'JUMPING';
        this.y_velocity = -config.JUMP_STRENGTH;
    }

    /**
     * Startet die Duck-Animation, wenn der Charakter dazu in der Lage ist.
     */
    duck() {
        if (this.state !== 'RUNNING') return;

        this.state = 'DUCKING_DOWN'; // Starte die Animationssequenz
    }
}