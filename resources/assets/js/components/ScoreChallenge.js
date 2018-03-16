import React, {Component} from 'react';
import { Link } from 'react-router-dom';
import {connect} from "react-redux";
import loadChalData from "../utils/loadChalData";

import RandomsPopup from "./RandomsPopup";

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
                    this.populateScores();
                })
        } else {
            console.log("ScoreChallenge - No need to load Challenge Data");
            this.populateScores();
        }
    }

    populateScores = () => {
        this.setState({ 'scores': this.props.challengeData[this.state.year][this.state.level][this.state.chalNum]
                .score_elements.reduce((acc, element)=> {
                    switch(element.type) {
                        case 'yesno':
                            acc[element.id] = element.base_value + element.multiplier;
                            break;
                        case 'high_slider':
                            acc[element.id] = element.max_entry;
                            break;
                        default:
                            acc[element.id] = element.base_value;
                    }
                    return acc;
                },{})
        },this.updateScore);
    };

    scoreChange = (scoreData) => {
        let total = {};
        total[scoreData.id] = scoreData.base + scoreData.val * scoreData.multi;
        this.setState({scores: Object.assign({},this.state.scores, total)});
        this.updateScore();
    };

    updateScore = () => {
        this.setState(
            {
                score: Math.max(0, Object.keys(this.state.scores).reduce((acc, key) => {
                    return acc + this.state.scores[key];
                },0))
            }
        )
    }

    challengeType = (type, item) => {
        switch(type) {
            case 'yesno':
                return <YesNo type="yesno" onChange={this.scoreChange} compInfo={this.state} key={item.id} {...item} />;
            case 'noyes':
                return <YesNo type="noyes" onChange={this.scoreChange} compInfo={this.state} key={item.id} {...item} />;
            case 'slider':
            case 'low_slider':
                return <Slider type="low" onChange={this.scoreChange} compInfo={this.state} key={item.id} {...item} />;
            case 'high_slider':
                return <Slider type="high" onChange={this.scoreChange} compInfo={this.state} key={item.id} {...item} />;
            case 'score_slider':
                return <Slider type="score" onChange={this.scoreChange} compInfo={this.state} key={item.id} {...item} />;
        }
        return <li>Unknown Type: {type}</li>;
    };

    render() {
        let chalData = (this.props.challengeData[this.state.year] &&
                          this.props.challengeData[this.state.year][this.state.level] &&
                          this.props.challengeData[this.state.year][this.state.level][this.state.chalNum]
                        ) ?
            this.props.challengeData[this.state.year][this.state.level][this.state.chalNum] : {};
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
                <ul className="ui-listview ui-listview-inset ui-corner-all ui-shadow">
                    {
                        (elements) ? elements.map((item,num) => {
                            return this.challengeType(item.type,item)}) :
                            <li>No Data</li>
                    }
                    <li className="ui-field-contain ui-li-static ui-body-inherit">
                        Estimated Score: {this.state.score} out of { chalData.points } points
                    </li>
                </ul>
            </div>
        )
    }
}

class YesNo extends  Component {
    componentDidMount() {
        this.$node = $(this.refs.flipswitch);
        this.$node.flipswitch();
        this.$node.on('change', this.sendScore);
        this.sendScore();
    }

    sendScore = () => {
        this.props.onChange({
            id: this.$node.data('id'),
            val: this.$node.val(),
            multi: this.$node.data('multi'),
            base: this.$node.data('base')
        });
    }

    shouldComponentUpdate() {
        return false;
    }

    componentWillUnmount() {
        this.$node.remove();
    }

    selectOrder = (type) => {
        if(type === 'noyes') {
            return [
                <option key="0" value="0">No</option>,
                <option key="1" value="1">Yes</option>
                ];
        } else {
            return [
                <option key="1" value="1">Yes</option>,
                <option key="0" value="0">No</option>
            ];
        }
    }

    render() {
        return (
            <li className="ui-field-contain ui-li-static ui-body-inherit">
                <h4 dangerouslySetInnerHTML={{ __html: this.props.display_text }} />
                <select ref="flipswitch"
                        data-role="flipswitch"
                        id={"sel_" + this.props.id}
                        data-id={this.props.id}
                        data-base={this.props.base_value}
                        data-multi={this.props.multiplier}
                        name={"scores[" + this.props.id + "][value]"}
                        className="ui-flipswitch-input"
                        tabIndex="-1"
                    >
                    { this.selectOrder(this.props.type) }
                </select>
            </li>
        )
    }
}

class Slider extends  Component {
    componentDidMount() {
        this.$node = $(this.refs.slider);
        this.$node.slider();
        this.$node.on('change', this.sendScore);
        this.sendScore()
    }

    shouldComponentUpdate() {
        return false;
    }

    componentWillUnmount() {
        this.$node.remove();
    }

    sendScore = () => {
        this.props.onChange({
            id: this.$node.data('id'),
            val: this.$node.val(),
            multi: this.$node.data('multi'),
            base: this.$node.data('base')
        });
    }

    render() {
        var propSet = {
            onChange: this.onChange,
            id: "sel_" +this.props.id,
            "data-id": this.props.id,
            "data-base": this.props.base_value,
            "data-multi": (this.props.type==='score_slider') ? 1 : this.props.multiplier,
            step: (this.props.type==='score_slider') ? this.props.multiplier : 1,
            name: "scores[" + this.props.id +"][value]",
            min: this.props.min_entry,
            max: this.props.max_entry,
            defaultValue: (this.props.type === 'high_slider') ? this.props.max_entry : 0,
        };
        
        return (
            <li className="ui-field-contain ui-li-static ui-body-inherit">
                <h4 dangerouslySetInnerHTML={{ __html: this.props.display_text }} />
                <input ref={"slider"}
                       {...propSet}
                       type="number"
                       data-type="range"
                       className="ui-clear-both ui-shadow-inset ui-body-inherit ui-corner-all ui-slider-input"
                    />
            </li>
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