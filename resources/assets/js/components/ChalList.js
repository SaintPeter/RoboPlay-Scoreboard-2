import React, {Component} from 'react';
import { Link } from 'react-router-dom';
import {connect} from "react-redux";
import loadChalData from "../utils/loadChalData";
import {loadChallengeData} from "../actions/ScoreChallenge";
import {updateBackButton, updatePageTitle} from "../actions/Generic";

class ChalListApp extends Component {
    constructor(props) {
        super(props);
        this.compId = props.match.params.compId;
        this.divId = props.match.params.divId;
        this.teamId = props.match.params.teamId;
        this.year = compData[this.compId].year;
        this.level = compData[this.compId].divisions[this.divId].level;
        this.competitionName = compData[this.compId].name;
        this.divisionName = compData[this.compId].divisions[this.divId].name;
        this.teamName = compData[this.compId].divisions[this.divId].teams[this.teamId];
    }

    componentWillMount() {
        this.props.updateTitle("Select Challenge");
        this.props.updateBack(`/c/${this.compId}/d/${this.divId}`);

        if(!this.props.challengeData[this.year] || !this.props.challengeData[this.year][this.level]) {
            loadChalData.load(this.year, this.level, this.props.doLoadChalData)
                .then((result) => {
                    console.log("Dispatch Result: ", result);
                })
        } else {
            console.log("ChalList - No need to load Challenge Data");
        }
    }



    render() {
        let challenges = (this.props.challengeData[this.year] && this.props.challengeData[this.year][this.level]) ?
            this.props.challengeData[this.year][this.level] : [];
        return (
            <div className="ui-content">
                <div className="ui-body ui-body-a ui-corner-all">
                    <strong>Judge: </strong>{judgeName}<br />
                    <strong>Division: </strong>{this.divisionName}<br />
                    <strong>Team: </strong>{this.teamName}
                </div>
                <ul className="ui-listview ui-listview-inset ui-corner-all ui-shadow">
                    {
                        (challenges) ? challenges.map((item,num) => {
                            return <Challenge
                                compId={this.compId}
                                divId={this.divId}
                                teamId={this.teamId}
                                key={item.id}
                                number={num} {...item} />}) :
                            <li>No Data</li>
                    }
                </ul>
            </div>
        )
    }
}

class Challenge extends  Component {
    render() {
        return (
        <li>
            <Link
                to={`/c/${this.props.compId}/d/${this.props.divId}/t/${this.props.teamId}/h/${this.props.number}`}
                className="ui-btn ui-btn-icon-right ui-icon-carat-r">
                {this.props.number + 1}. {this.props.display_name} ({this.props.points} Points Possible)
            </Link>
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
        updateBack: (newURL) => dispatch(updateBackButton(newURL)),
        updateTitle: (newTitle) => dispatch(updatePageTitle(newTitle)),
        doLoadChalData: (year,level,data) => dispatch(loadChallengeData(year,level,data))
    }
}

const ChalList =  connect(
    mapStateToProps,
    mapDispatchToProps
)(ChalListApp);

export default ChalList;