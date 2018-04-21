import React, {Component} from 'react';
import { Link } from 'react-router-dom';
import {connect} from "react-redux";
import {updateBackButton, updatePageTitle} from "../actions/Generic";

class DivListApp extends Component {
    constructor(props) {
        super(props);
        this.compId = props.match.params.compId;
        this.competitionName = compData[this.compId].name;
        this.divs = Object.keys(compData[this.compId].divisions).reduce((list,divId) => {
                list.push( {
                    key: divId,
                    divId: divId,
                    compId: this.compId,
                    name: compData[this.compId].divisions[divId].name
                });
                return list;
            },[]);

    }

    componentDidMount() {
        this.props.updateTitle("Choose Division");
        this.props.updateBack('/',true);
    }

    render() {
        return (
            <div className="ui-content">
                <div className="ui-body ui-body-a ui-corner-all">
                    <strong>Competition:</strong> {this.competitionName}
                </div>
                <ul className="ui-listview listview-spacer">
                    {
                        this.divs.map(item => {
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
    return {}
}

// Map Redux actions to component props
function mapDispatchToProps(dispatch) {
    return {
        updateBack: (newURL,show) => dispatch(updateBackButton(newURL,show)),
        updateTitle: (newTitle) => dispatch(updatePageTitle(newTitle))
    }
}

const DivList =  connect(
    mapStateToProps,
    mapDispatchToProps
)(DivListApp);

export default DivList;