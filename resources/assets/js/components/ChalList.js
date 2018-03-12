import React, {Component} from 'react';
import { Link } from 'react-router-dom';
import {connect} from "react-redux";
import loadChalData from "../utils/loadChalData";

class ChalListApp extends Component {
    constructor(props) {
        super(props);
        const compId = props.match.params.compId;
        const divId = props.match.params.divId;
        const teamId = props.match.params.teamId;
        const year = compData[compId].year;
        const level = compData[compId].divisions[divId].level;
        this.state = {
            compId: compId,
            divId: divId,
            teamId: teamId,
            year: year,
            level: level,
            competitionName: compData[compId].name,
            divisionName: compData[compId].divisions[divId].name,
            teamName: compData[compId].divisions[divId].teams[teamId]
        }
    }

    componentDidMount() {
        this.props.updateTitle("Select Challenge");
        this.props.updateBack(`/c/${this.state.compId}/d/${this.state.divId}`);

        if(!this.props.challengeData[this.state.year] || !this.props.challengeData[this.state.year][this.state.level]) {
            loadChalData.load(this.state.year, this.state.level, this.props.doLoadChalData)
        } else {
            console.log("ChalList - No need to load Challenge Data");
        }
    }

    render() {
        let challenges = (this.props.challengeData[this.state.year] && this.props.challengeData[this.state.year][this.state.level]) ?
            this.props.challengeData[this.state.year][this.state.level] : [];
        return (
            <div className="ui-content">
                <div className="ui-body ui-body-a ui-corner-all">
                    <strong>Judge: </strong>{judgeName}<br />
                    <strong>Division: </strong>{this.state.divisionName}<br />
                    <strong>Team: </strong>{this.state.teamName}
                </div>
                <ul className="ui-listview ui-listview-inset ui-corner-all ui-shadow">
                    {
                        (challenges) ? challenges.map((item,num) => {
                            return <Challenge compInfo={this.state} key={item.id} number={num} {...item} />}) :
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
                to={`/c/${this.props.compInfo.compId}/d/${this.props.compInfo.divId}/t/${this.props.compInfo.teamId}/h/${this.props.number}`}
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
        updateBack: (newURL) => dispatch({ type: 'change_url', url: newURL}),
        updateTitle: (newTitle) => dispatch({ type: 'change_title', title: newTitle }),
        doLoadChalData: (year,level,data) =>dispatch({ type: 'load_chal_data', 'year': year, 'level': level, 'data': data})
    }
}

const ChalList =  connect(
    mapStateToProps,
    mapDispatchToProps
)(ChalListApp);

export default ChalList;