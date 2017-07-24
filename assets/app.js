
class Robot {

    constructor(){
        this.boundEvents = {
            move: this.moveRobot,
            left: this.leftRobot,
            right: this.rightRobot,
            place: this.placeRobot,
            report: this.reportRobot
        };

        this.boundElements = {
            buttonPanel: document.getElementById('button-panel'),
            robot: document.getElementById('robot'),
            placeInput: document.getElementById('command_option'),
            messages: document.getElementById('messages'),
        };

        this.placed = false;
        this.degrees = null;

        this.bindButtons();
    }

    bindButtons(){
        for(let element in this.boundEvents){
            document.getElementById('button-'+element).addEventListener('click', this.boundEvents[element].bind(this));
        }
    }

    async moveRobot(){
        const json = await this.sendCommand('MOVE');
        this.positionRobot(json);
        this.updatePosition(json);
    }

    async leftRobot(){
        const json = await this.sendCommand('LEFT');
        this.positionRobot(json);
        this.updatePosition(json);
    }
    async rightRobot(){
        const json = await this.sendCommand('RIGHT');
        this.positionRobot(json);
        this.updatePosition(json);
    }

    async placeRobot(){
        const option = document.getElementById('command_option').value;
        const command = `PLACE ${option}`;
        const json = await this.sendCommand(command);
        this.placed = true;

        this.boundElements.buttonPanel.style.display = "block";

        this.boundElements.robot.style.opacity = 1;

        this.positionRobot(json);
        this.updatePosition(json);
    }

    positionRobot(json){
        if(json.x_position === undefined){
            return false;
        }
        const leftPosition = (json.x_position * 100 - 100) + 'px';
        const topPosition = (500 - json.y_position * 100) + 'px';

        const degrees = `rotate(${json.degrees}deg)`;
        this.boundElements.robot.style.left = leftPosition;
        this.boundElements.robot.style.top = topPosition;
        this.boundElements.robot.style.transform = degrees;

    }

    updatePosition(json){
        if(json.x_position === undefined){
            return false;
        }

        const leftPosition = (json.x_position * 100 - 100) + 'px';
        const topPosition = (json.y_position * 100 - 100) + 'px';

        const textString = `${json.x_position},${json.y_position},${json.facing}`;
        this.boundElements.placeInput.value = textString;
    }

    async reportRobot() {
        const json = await self.sendCommand('REPORT');
    }

    async sendCommand(command = 'MOVE'){
        const body = JSON.stringify({command});
        const response = await fetch('/command.php', {method: 'POST', body: body, credentials: 'same-origin'});
        const data = await response.json();

        if(!response.ok){
            this.boundElements.messages.style.display = 'block';
            this.boundElements.messages.innerText = data.error;
        } else {
            console.log('No Error');
            this.boundElements.messages.style.display = 'none';
            this.boundElements.messages.value = '';
        }

        return data;
    }

}

let robot = new Robot();