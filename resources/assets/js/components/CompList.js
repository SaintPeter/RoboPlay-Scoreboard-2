import React, {Component} from 'react';
import { Link } from 'react-router-dom';
import {connect} from "react-redux";

class CompListApp extends Component {
    constructor(props) {
        super(props);
        this.state = this.readData(props);
    }

    componentWillReceiveProps(nextProps) {
        this.setState(this.readData(nextProps));
    }

    componentDidMount() {
        document.getElementById('header').innerHTML = "Competition List";
    }

    readData(props) {
        let newState = {comps: []};

        newState.comps = Object.keys(compData).reduce((list, compId) => {
            list.push({
                key: compId,
                id: compId,
                name: compData[compId].name
            });
            return list;
        }, []);

        return newState;
    }

    render() {
        return (
            <div className="ui-content">
                <ul className="ui-listview">
                    {
                        this.state.comps.map(item => {
                            return <Comp {...item} />})
                    }
                </ul>
            </div>
        )
    }
}

class Comp extends  Component {
    render() {
        return <li><Link to={`/c/${this.props.id}`} className="ui-btn ui-btn-icon-right ui-icon-carat-r">{this.props.name}</Link></li>
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
        updateTitle: (newTitle) => dispatch({ type: 'change_title', title: newTitle })
    }
}

const CompList =  connect(
    mapStateToProps,
    mapDispatchToProps
)(CompListApp);

export default CompList;