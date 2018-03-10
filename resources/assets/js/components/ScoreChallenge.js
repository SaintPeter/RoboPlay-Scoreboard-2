import React, {Component} from 'react';
import { Link } from 'react-router-dom';
import {connect} from "react-redux";
import loadChalData from "../utils/loadChalData";

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
        }
    }

    componentDidMount() {
        this.props.updateTitle("Score");
        this.props.updateBack(`/c/${this.state.compId}/d/${this.state.divId}/t/${this.state.teamId}`);

        if(!this.props.challengeData[this.state.year] || !this.props.challengeData[this.state.year][this.state.level]) {
            loadChalData.load(this.state.year, this.state.level, this.props.doLoadChalData)
        } else {
            console.log("ScoreChallenge - No need to load Challenge Data");
        }
    }

    challengeType = (type, item) => {
        switch(type) {
            case 'yesno':
                return <YesNo type="yesno" compInfo={this.state} key={item.id} {...item} />;
            case 'noyes':
                return <YesNo type="noyes" compInfo={this.state} key={item.id} {...item} />;
            case 'slider':
            case 'low_slider':
                return <Slider type="low" compInfo={this.state} key={item.id} {...item} />;
            case 'high_slider':
                return <Slider type="high" compInfo={this.state} key={item.id} {...item} />;
            case 'score_slider':
                return <Slider type="score" compInfo={this.state} key={item.id} {...item} />;
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
                <ul className="ui-listview ui-listview-inset ui-corner-all ui-shadow">
                    {
                        (elements) ? elements.map((item,num) => {
                            return this.challengeType(item.type,item)}) :
                            <li>No Data</li>
                    }
                </ul>
            </div>
        )
    }
}

class YesNo extends  Component {
    componentDidMount() {
        this.$node = $(this.refs.flipswitch);

        this.$node.flipswitch({
            change: (event, ui) => this.props.onChange(event, ui)
        });
    }

    shouldComponentUpdate() {
        return false;
    }

    componentWillUnmount() {
        this.$node.remove();
    }

    selectOrder = (type) => {
        if(type === 'yesno') {
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
                <div ref="flipswitch" className="ui-flipswitch ui-shadow-inset ui-bar-inherit ui-corner-all ui-flipswitch-active">
                    <a href="#" className="ui-flipswitch-on ui-btn ui-shadow ui-btn-inherit">Yes</a>
                    <span className="ui-flipswitch-off">No</span>
                    <select id="sel_{this.props.id}"
                            data-base="{this.props.base_value}"
                            data-multi="{this.props.multiplier}"
                            name="scores[{this.props.id}][value]"
                            data-role="flipswitch"
                            className="ui-flipswitch-input"
                            tabIndex="-1"
                    >
                        { this.selectOrder(this.props.type) }
                    </select>
                </div>
            </li>
        )
    }
}

class Slider extends  Component {
    componentDidMount() {
        this.$node = $(this.refs.slider);

        this.$node.slider({
            change: (event, ui) => this.onChange(event, ui)
        });
    }

    shouldComponentUpdate() {
        return false;
    }

    componentWillUnmount() {
        this.$node.remove();
    }

    onChange = (e,ui) => {  };

    render() {
        return (
            <li className="ui-field-contain ui-li-static ui-body-inherit">
                <h4 dangerouslySetInnerHTML={{ __html: this.props.display_text }} />
                <input ref={"slider"}
                       onChange={this.onChange}
                       id={"sel_" +this.props.id}
                       data-base={this.props.base_value}
                       data-multi={this.props.multiplier}
                       name={"scores[" + this.props.id +"][value]"}
                       min={this.props.min_entry}
                       max={this.props.max_entry}
                       value="0"
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
        doLoadChalData: (year,level,data) =>dispatch({ type: 'load_chal_data', 'year': year, 'level': level, 'data': data})
    }
}

const ScoreChallenge =  connect(
    mapStateToProps,
    mapDispatchToProps
)(ScoreChallengeApp);

export default ScoreChallenge;