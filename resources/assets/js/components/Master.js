import  React, { Component} from 'react';
import { BrowserRouter , Route, Switch, Link } from 'react-router-dom';
import { connect } from 'react-redux';

import CompList from "./CompList";
import DivList from "./DivList";
import TeamList from "./TeamList";
import ChalList from "./ChalList";
import ScoreChallenge from "./ScoreChallenge";
import TestChallenge from "./TestChallenge";
import {updateBackButton} from "../actions/Generic";
import {clearRuns} from "../actions/Runs";
import {submitScores, updateScoreSummary, clearAllScores} from "../actions/ScoreChallenge";
import {clearChallengeData} from "../actions/ChallengeData";
import SettingsPopup from "./Popups/SettingsPopup";
import TeamView from "./TeamView";


class MasterApp extends Component {
    constructor(props) {
        super(props);
        document.title = "Choose Competition";
        this.state = {
            settingsVisible: false
        }
    }

    componentDidUpdate(prevProps) {
        document.title = this.props.title;
    }

    componentDidMount() {
        this.props.updateScoreSummary(this.props.teamScores);

        console.log('Starting Timer');
        let intervalId = setInterval(this.scoreSendingTimer.bind(this), 1000 * 60);
        this.setState({intervalId: intervalId});
    }

    componentWillUnmount() {
        clearInterval(this.state.intervalId);
    }

    scoreSendingTimer() {
        console.log("Submitting Scores");
        this.props.submitScores(this.props.teamScores)
    }

    toggleSettings = (e) => {
        e.preventDefault();
        this.setState({settingsVisible: !this.state.settingsVisible});
    };

    // Settings Popup Hanlders
    settingsClosed = (e) => {
        this.setState({settingsVisible: false});
    };

    render(){
        return (
            <BrowserRouter basename="scorer">
                <div className="ui-page ui-page-theme-a ui-page-active">
                    <div className="ui-header ui-bar-inherit">
                        <h1 id="header" className="ui-title">{this.props.title}</h1>
                        <a href="/" className="ui-btn-left ui-link ui-btn ui-icon-home ui-btn-icon-notext ui-shadow ui-corner-all" data-icon="home" data-ajax="false" data-iconpos="notext" data-direction="reverse">Home</a>
                        { (this.props.backShow) ? <Link to={this.props.backURL} id="back_button" className="ui-btn-right ui-link ui-btn ui-icon-back ui-btn-icon-notext ui-shadow ui-corner-all">Back</Link> : '' }
                    </div>

                    <Switch>
                        <Route exact path="/" component={CompList} />
                        <Route exact path="/c/:compId" component={DivList} />
                        <Route exact path="/c/:compId/d/:divId" component={TeamList} />
                        <Route exact path="/c/:compId/d/:divId/t/:teamId" component={ChalList} />
                        <Route exact path="/c/:compId/d/:divId/t/:teamId/h/:chalId" component={ScoreChallenge} />
                        <Route exact path="/c/:compId/d/:divId/t/:teamId/scores" component={TeamView} />
                        <Route exact path="/test/:chalId" component={TestChallenge} />
                    </Switch>


                    <div className="ui-footer ui-bar-inherit">
                        <h4>
                            <div style={{ textAlign: 'center'}}>
                                <span onClick={this.scoreSendingTimer.bind(this)} className="score-status">
                                    Saved: {this.props.scoreSummary.s} &nbsp;
                                    Unsaved: {this.props.scoreSummary.u}
                                </span>
                                <span onClick={this.toggleSettings}
                                    className="ui-btn-right ui-btn ui-icon-gear ui-btn-icon-notext ui-shadow ui-corner-all">
                                </span>
                            </div>
                        </h4>
                    </div>
                    <SettingsPopup
                        onCancel={this.settingsClosed}
                        clearChallengeDataClick={this.props.clearChallengeData}
                        clearRunsClick={this.props.clearRuns}
                        clearScoresClick={this.props.clearAllScores}
                        visible={this.state.settingsVisible}
                    />
                </div>
            </BrowserRouter>

        )
    }
}

// Map Redux state to component props
function mapStateToProps(state) {
    return {
        backURL: state.generic.backURL,
        backShow: state.generic.backShow,
        title: state.generic.title,
        teamScores: state.teamScores,
        scoreSummary: state.scoreSummary
    }
}

// Map Redux actions to component props
function mapDispatchToProps(dispatch) {
    return {
        updateBack: (newURL, show) => dispatch(updateBackButton(newURL, show)),
        updateScoreSummary: (teamScores) => dispatch(updateScoreSummary(teamScores)),
        submitScores: (teamScores) => dispatch(submitScores(teamScores)),
        clearChallengeData: () => dispatch(clearChallengeData()),
        clearRuns: () => dispatch(clearRuns()),
        clearAllScores: () => dispatch(clearAllScores())
    }
}

const Master =  connect(
    mapStateToProps,
    mapDispatchToProps
)(MasterApp);

export default Master;