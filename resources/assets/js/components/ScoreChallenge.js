import React, {Component} from 'react';
import { Link } from 'react-router-dom';
import {connect} from "react-redux";
import loadChalData from "../utils/loadChalData";

import RandomsPopup from "./RandomsPopup";
import RandomListPopup from "./RandomListPopup";
import YesNo from "./ScoreElements/YesNo";
import Slider from "./ScoreElements/Slider";
import ScoreElement from "./ScoreElements/ScoreElement";

class ScoreChallengeApp extends Component {
    constructor(props) {
        super(props);
        const compId = props.match.params.compId;
        const divId = props.match.params.divId;
        const teamId = props.match.params.teamId;
        const chalNum = props.match.params.chalId;
        const year = compData[compId].year;
        const level = compData[compId].divisions[divId].level;
        this.state = {
            compId: compId,
            divId: divId,
            teamId: teamId,
            chalNum: chalNum,
            year: year,
            level: level,
            competitionName: compData[compId].name,
            divisionName: compData[compId].divisions[divId].name,
            teamName: compData[compId].divisions[divId].teams[teamId],
            score: 0,
            scores: {}
        }
    }

    componentWillMount() {
        this.props.updateTitle("Score");
        this.props.updateBack(`/c/${this.state.compId}/d/${this.state.divId}/t/${this.state.teamId}`);

        if(!this.props.challengeData[this.state.year] || !this.props.challengeData[this.state.year][this.state.level]) {
            loadChalData.load(this.state.year, this.state.level, this.props.doLoadChalData)
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
        this.setState({ 'scores': this.props.challengeData[this.state.year][this.state.level][this.state.chalNum]
                .score_elements.reduce((acc, element)=> {
                    switch(element.type) {
                        case 'yesno':
                            acc[element.element_number] = {
                                id: element.id,
                                score: element.base_value + element.multiplier };
                            break;
                        case 'high_slider':
                            acc[element.element_number] = {
                                id: element.id,
                                score: element.max_entry * element.multiplier};
                            break;
                        default:
                            acc[element.element_number] = {
                                id: element.id,
                                score: element.base_value};
                    }
                    return acc;
                },{})
        },
        this.updateTotalScore);
    };

    updateTotalScore = () => {
        this.setState(
            {
                score: Math.max(0, Object.keys(this.state.scores).reduce((acc, key) => {
                    return acc + this.state.scores[key].score;
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
        }
        return <li>Unknown Type: {type}</li>;
    };

    render() {
        let chalData = {};
        if(this.props.challengeData[this.state.year] &&
                          this.props.challengeData[this.state.year][this.state.level] &&
                          this.props.challengeData[this.state.year][this.state.level][this.state.chalNum]
                        ) {
            chalData = this.props.challengeData[this.state.year][this.state.level][this.state.chalNum];
            chalData.rules = chalData.rules.replace(/\r\n/g,"<br />");
        }
        let elements = (chalData.score_elements) ? chalData.score_elements : [];

        return (
            <div className="ui-content">
                <div className="ui-body ui-body-a ui-corner-all">
                    <strong>Judge: </strong>{judgeName}<br />
                    <strong>Division: </strong>{this.state.divisionName}<br />
                    <strong>Team: </strong>{this.state.teamName}
                    <h1>Run X</h1>
                    <strong>{parseInt(this.state.chalNum,10) + 1}. {chalData.display_name}</strong>
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
                        Estimated Score: {this.state.score} out of { chalData.points } points
                    </li>
                </ul>
            </div>
        )
    }
}

// Map Redux state to component props
function mapStateToProps(state) {
    return {
        challengeData: state.challengeData,
        backURL: state.backURL
    }
}

// Map Redux actions to component props
function mapDispatchToProps(dispatch) {
    return {
        updateBack: (newURL) => dispatch({ type: 'change_url', url: newURL}),
        updateTitle: (newTitle) => dispatch({ type: 'change_title', title: newTitle }),
        doLoadChalData: (year,level,data) => dispatch({ type: 'load_chal_data', 'year': year, 'level': level, 'data': data})
    }
}

const ScoreChallenge =  connect(
    mapStateToProps,
    mapDispatchToProps
)(ScoreChallengeApp);

export default ScoreChallenge;