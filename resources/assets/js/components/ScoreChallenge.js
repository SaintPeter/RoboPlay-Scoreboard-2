import React, {Component} from 'react';
import { Link } from 'react-router-dom';
import { connect } from "react-redux";
import loadChalData from "../utils/loadChalData";

import RandomsPopup from "./Popups/RandomsPopup";
import RandomListPopup from "./Popups/RandomListPopup";
import UIButton from "./UIButton";
import YesNo from "./ScoreElements/YesNo";
import Slider from "./ScoreElements/Slider";
import Timer from "./ScoreElements/Timer";
import ScoreElement from "./ScoreElements/ScoreElement";
import SubmitConfirmPopup from "./Popups/SubmitConfirmPopup";
import AbortConfirmPopup from "./Popups/AbortConfirmPopup";
import {abortChallenge, loadChallengeData, scoreChallenge, updateScoreSummary} from "../actions/ScoreChallenge";
import {updateBackButton, updatePageTitle} from "../actions/Generic";

class ScoreChallengeApp extends Component {
    constructor(props) {
        super(props);
        this.compId = props.match.params.compId;
        this.divId = props.match.params.divId;
        this.teamId = props.match.params.teamId;
        this.year = compData[this.compId].year;
        this.level = compData[this.compId].divisions[this.divId].level;
        this.chalNum = props.match.params.chalId;
        this.chalId = this.props.challengeData[this.year][this.level][this.chalNum].id;
        this.divisionName =  compData[this.compId].divisions[this.divId].name;
        this.teamName = compData[this.compId].divisions[this.divId].teams[this.teamId];

        this.backURL = `/c/${this.compId}/d/${this.divId}/t/${this.teamId}`;
        
        // TODO:  Import Run Number
        this.runNumber = 0;
        
        this.state = {
            total: 0,
            scores: {},
            submitConfirmVisible: false,
            abortConfirmVisible: false
        };
        
    }

    componentWillMount() {
        this.props.updateTitle("Score");
        this.props.updateBack(this.backURL);

        if(!this.props.challengeData[this.year] || !this.props.challengeData[this.year][this.level]) {
            loadChalData.load(this.year, this.level, this.props.doLoadChalData)
                .then((result) => {
                    console.log("Dispatch Result: ", result);
                    this.prePopulateStateScores();
                })
        } else {
            console.log("ScoreChallenge - No need to load Challenge Data");
            this.prePopulateStateScores();
        }
    }

    prePopulateStateScores = () => {
        this.setState({ 'scores': this.props.challengeData[this.year][this.level][this.chalNum]
                .score_elements.reduce((acc, element)=> {
                    switch(element.type) {
                        case 'yesno':
                            acc[element.element_number] = element.base_value + element.multiplier ;
                            break;
                        case 'high_slider':
                            acc[element.element_number] = element.max_entry * element.multiplier;
                            break;
                        default:
                            acc[element.element_number] = element.base_value;
                    }
                    return acc;
                },{})
        },
        this.updateTotalScore);
    };

    updateTotalScore = () => {
        this.setState(
            {
                total: Math.max(0, Object.keys(this.state.scores).reduce((acc, key) => {
                    return acc + this.state.scores[key];
                },0))
            }
        )
    };

    // Handles the change from an individual score element
    // Updates the state, then updates the total
    scoreChange = (scoreData) => {
        let newState = {scores: Object.assign({},this.state.scores, scoreData)};
        this.setState(newState,
            this.updateTotalScore);
    };

    challengeType = (type, item) => {
        switch(type) {
            case 'yesno':
                return <YesNo type="yesno" compInfo={this.state} key={item.id} {...item} />;
            case 'noyes':
                return <YesNo type="noyes" compInfo={this.state} key={item.id} {...item} />;
            case 'slider':
            case 'low_slider':
                return <Slider type="low"  compInfo={this.state} key={item.id} {...item} />;
            case 'high_slider':
                return <Slider type="high"  compInfo={this.state} key={item.id} {...item} />;
            case 'score_slider':
                return <Slider type="score"  compInfo={this.state} key={item.id} {...item} />;
            case 'timer':
                return <Timer type="timer" compInfo={this.state} key={item.id} {...item} />;
        }
        return <li>Unknown Type: {type}</li>;
    };

    submitConfirmCanceled = () => {
      this.setState({ submitConfirmVisible: false });
    };
    
    submitConfirmConfirmed = () => {
        this.setState({ submitConfirmVisible: false });
        this.props.submitScore(this.teamId, this.chalId, this.divId, this.state.scores);
        this.props.history.push(this.backURL);
    };

    abortConfirmCanceled = () => {
        this.setState({ abortConfirmVisible: false });
    };

    abortConfirmConfirmed = () => {
        this.setState({ abortConfirmVisible: false });
        this.props.submitAbort(
            this.teamId,
            this.chalId,
            this.divId,
            this.props.challengeData[this.year][this.level][this.chalNum].score_elements.length
        );
        this.props.history.push(this.backURL);
    };

    render() {
        let chalData = {};
        if(this.props.challengeData[this.year] &&
                          this.props.challengeData[this.year][this.level] &&
                          this.props.challengeData[this.year][this.level][this.chalNum]
                        ) {
            chalData = this.props.challengeData[this.year][this.level][this.chalNum];
            chalData.rules = chalData.rules.replace(/\r\n/g,"<br />");
        }
        let elements = (chalData.score_elements) ? chalData.score_elements : [];

        return (
            <div className="ui-content">
                <div className="ui-body ui-body-a ui-corner-all">
                    <strong>Judge: </strong>{judgeName}<br />
                    <strong>Division: </strong>{this.divisionName}<br />
                    <strong>Team: </strong>{this.teamName}
                    <h1>Run { this.runNumber }</h1>
                    <strong>{parseInt(this.chalNum,10) + 1}. {chalData.display_name}</strong>
                    <hr />
                    <div dangerouslySetInnerHTML={{__html: chalData.rules}} />
                </div>
                {
                    (chalData.randoms && chalData.randoms.length > 0) ?
                        <RandomsPopup randoms={chalData.randoms}/> : ''
                }
                {
                    (chalData.random_lists && chalData.random_lists.length > 0) ?
                        <RandomListPopup random_lists={chalData.random_lists}/> : ''
                }
                <ul className="ui-listview ui-listview-inset ui-corner-all ui-shadow">
                    {
                        (elements) ? elements.map((item,num) => {
                            return (
                                <ScoreElement key={item.id} scoreChange={this.scoreChange} {...item}>
                                    {this.challengeType(item.type,item)}
                                </ScoreElement>
                            )
                        }) : <li>No Data</li>
                    }
                    <li className="ui-field-contain ui-li-static ui-body-inherit">
                        Estimated Score: {this.state.total} out of { chalData.points } points
                    </li>
                    <li className="ui-field-contain ui-li-static ui-body-inherit">
                        <fieldset className="ui-grid-b">
                            <div className="ui-block-b">
                                <UIButton onClick={(e) => { this.setState({ submitConfirmVisible: true }) }} >
                                    Submit
                                </UIButton>
                            </div>
                            <div className="ui-block-b">
                                <Link to={ this.backURL }
                                      className="ui-btn ui-input-btn ui-corner-all ui-shadow">
                                    Cancel
                                </Link>
                            </div>
                            <div className="ui-block-b">
                                <UIButton onClick={(e) => { this.setState({ abortConfirmVisible: true }) }}>
                                    Abort
                                </UIButton>
                            </div>
                        </fieldset>
                    </li>
                </ul>
                <SubmitConfirmPopup
                    onSubmit={ this.submitConfirmConfirmed }
                    onCancel={ this.submitConfirmCanceled }
                    runNumber={ this.runNumber }
                    visible={ this.state.submitConfirmVisible }
                    score={ this.state.total }
                />
                <AbortConfirmPopup
                    onAbort={ this.abortConfirmConfirmed }
                    onCancel={ this.abortConfirmCanceled }
                    runNumber={ this.runNumber }
                    visible={ this.state.abortConfirmVisible }
                />
            </div>
        )
    }
}

// Map Redux state to component props
function mapStateToProps(state) {
    return {
        teamScores: state.teamScores,
        challengeData: state.challengeData,
        backURL: state.generic.backURL
    }
}

// Map Redux actions to component props
function mapDispatchToProps(dispatch) {
    return {
        updateBack: (newURL) => dispatch(updateBackButton(newURL)),
        updateTitle: (newTitle) => dispatch(updatePageTitle(newTitle)),
        doLoadChalData: (year,level,data) => dispatch(loadChallengeData(year,level,data)),
        submitScore: (teamId, chalId, divId, scores) => dispatch(scoreChallenge(teamId,chalId,divId,scores)),
        submitAbort: (teamId, chalId, divId, elementCount) => dispatch(abortChallenge(teamId,chalId,divId,elementCount)),
        updateScoreSummary: (teamScores) => dispatch(updateScoreSummary(teamScores))
    }
}

const ScoreChallenge =  connect(
    mapStateToProps,
    mapDispatchToProps
)(ScoreChallengeApp);

export default ScoreChallenge;