import React, {Component} from 'react';
import { Link } from 'react-router-dom';
import {connect} from "react-redux";
import loadChalData from '../utils/loadChalData';

class DivListApp extends Component {
    constructor(props) {
        super(props);
        const compId = props.match.params.compId;
        this.state = {
            competitionName: compData[compId].name,
            divs: Object.keys(compData[compId].divisions).reduce((list,divId) => {
                list.push( {
                    key: divId,
                    compId: compId,
                    divId: divId,
                    name: compData[compId].divisions[divId].name
                });
                return list;
            },[])
        }
    }

    componentDidMount() {
        this.props.updateTitle("Choose Division");
        this.props.updateBack('/');
    }

    render() {
        return (
            <div className="ui-content">
                <h4>Competition: {this.state.competitionName}</h4>
                <ul className="ui-listview listview-spacer">
                    {
                        this.state.divs.map(item => {
                            return <Division {...item} />})
                    }
                </ul>
            </div>
        )
    }
}

class Division extends  Component {
    render() {
        return <li><Link to={`/c/${this.props.compId}/d/${this.props.divId}`} className="ui-btn ui-btn-icon-right ui-icon-carat-r">{this.props.name}</Link></li>
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

const DivList =  connect(
    mapStateToProps,
    mapDispatchToProps
)(DivListApp);

export default DivList;