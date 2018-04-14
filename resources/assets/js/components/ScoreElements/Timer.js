import React, {Component} from "react";
import {connect} from "react-redux";
import {updateBackButton, updatePageTitle} from "../../actions/Generic";
import {abortChallenge, loadChallengeData, scoreChallenge, updateScoreSummary} from "../../actions/ScoreChallenge";

// Thanks to Kurt Johnson (@pompan129) for the code!

const resetButtonStyle = { width: "5em", padding: "5px" };
const playButtonStyle = { width: "5em", padding: "5px", marginLeft: "5px" };

const timeDivStyle = {
    width: "5em",
    border: "1px solid #ddd",
    padding: "5px"
};

const addButtonStyle = {
    width: "3em",
    padding: "5px",
    borderRadius: ".3125em 0 0 .3125em "
};

const minusButtonStyle = {
    width: "3em",
    padding: "5px",
    borderRadius: "0 .3125em .3125em 0"
};

const containerStyle = {
    display: "flex",
    justifyContent: "center",
    alignItems: "baseline"
};


export default class Timer extends Component {
    constructor(props) {
        super(props);
        this.state = {
            seconds: "0.0",
            playing: false
        };
    }

    componentDidMount() {
        this.sendScore();
    }

    add() {
        if (!this.state.playing) {
            const time = Number(this.state.seconds) + 1.0;
            this.setState({ seconds: time.toFixed(1) },this.sendScore.bind(this));
        }
    }

    subtract() {
        if (!this.state.playing && this.state.seconds > 0.0) {
            const time = Number(this.state.seconds) - 1.0;
            this.setState({ seconds: time.toFixed(1) }, this.sendScore.bind(this));
        }
    }

    tick() {
        if (this.state.playing) {
            const time = Number(this.state.seconds) + 0.1;
            this.setState({ seconds: time.toFixed(1) });
        }
    }

    play() {
        if (!this.state.playing) {
            this.setState({ playing: true });
            this.timerID = setInterval(() => this.tick(), 100);
        }
    }

    stop() {
        if (this.state.playing) {
            this.setState({ playing: false },this.sendScore.bind(this));
            clearInterval(this.timerID);
        }
    }

    reset() {
        this.setState({
            seconds: 0.0
        },this.sendScore.bind(this));
    }

    sendScore = seconds => {
        this.props.onChange(Math.floor(Number(this.state.seconds)));
    };

    render() {
        return (
            <div style={containerStyle}>
                <button onClick={() => this.add()} style={addButtonStyle}>
                    +
                </button>
                <div style={timeDivStyle}>
                    <span>{this.state.seconds} sec</span>
                </div>
                <button onClick={() => this.subtract()} style={minusButtonStyle}>
                    -
                </button>

                <button
                    onClick={this.state.playing ? () => this.stop() : () => this.play()}
                    className="ui-btn ui-input-btn ui-corner-all ui-shadow"
                >
                    {this.state.playing ? "Stop" : "Start"}
                </button>
                <button onClick={() => this.reset()} className="ui-btn ui-input-btn ui-corner-all ui-shadow">
                    Reset
                </button>
            </div>
        );
    }
}