import * as config from './config.js';

export class Enemy {
    constructor() {
        this.element = document.getElementById(config.ENEMY_ID);
        this.currentLeft = 0;
        this.speed = config.INITIAL_ENEMY_SPEED;
        this.obstacleType = 1; // 1: normal (jump), 2: very high (duck)
    }

    resetPosition() {
        const charData = config.getSelectedCharacter();
        this.element.style.backgroundImage = `url('${charData.enemy}')`;
        this.currentLeft = window.innerWidth + 200; // Start off-screen to the right
        this.element.style.left = this.currentLeft + 'px';
        this.randomizeObstacleType();
    }

    resetSpeed() {
        this.speed = config.INITIAL_ENEMY_SPEED;
    }

    move() {
        this.currentLeft -= this.speed;
        this.element.style.left = this.currentLeft + 'px';

        if (this.currentLeft < -400) { // Well off-screen to the left
            this.resetPosition();
            return true; // Indicates enemy has passed and reset
        }
        return false;
    }

    increaseSpeed() {
        this.speed *= config.ENEMY_SPEED_INCREASE_FACTOR;
    }

    randomizeObstacleType() {
        // Nur noch 2 Typen: 1 (Springen) und 2 (Ducken)
        this.obstacleType = Math.floor(Math.random() * 2) + 1;
        switch (this.obstacleType) {
            case 1: // normal (zum Springen)
                this.element.style.top = config.ENEMY_NORMAL_POS_TOP;
                break;
            case 2: // high (zum Ducken)
                this.element.style.top = config.ENEMY_VERY_HIGH_POS_TOP;
                break;
        }
    }


}