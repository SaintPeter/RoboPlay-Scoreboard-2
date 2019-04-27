import React, {Component} from 'react';
import {Link} from 'react-router-dom';
import {connect} from "react-redux";
import "../actions/Generic";
import {updateBackButton, updatePageTitle} from "../actions/Generic";

class CompListApp extends Component {
  constructor(props) {
    super(props);

    // compData is passed in via a global in the hosting html document
    this.comps = Object.keys(compData).reduce((list, compId) => {
      list.push({
        key: compId,
        id: compId,
        name: compData[compId].name
      });
      return list;
    }, []);
  }

  componentDidMount() {
    document.getElementById('header').innerHTML = "Competition List";
    this.props.updateTitle('Choose Competition');
    this.props.updateBack('', false);
  }

  render() {
    return (
      <ul className="ui-listview">
        {
          this.comps.map(item => {
            return <Comp {...item} />
          })
        }
      </ul>
    )
  }
}

class Comp extends Component {
  render() {
    return <li><Link to={`/c/${this.props.id}`}
                     className="ui-btn ui-btn-icon-right ui-icon-carat-r">{this.props.name}</Link></li>
  }
}

// Map Redux state to component props
function mapStateToProps(state) {
  return {}
}

// Map Redux actions to component props
function mapDispatchToProps(dispatch) {
  return {
    updateBack: (newURL, show) => dispatch(updateBackButton(newURL, show)),
    updateTitle: (newTitle) => dispatch(updatePageTitle(newTitle))
  }
}

const CompList = connect(
  mapStateToProps,
  mapDispatchToProps
)(CompListApp);

export default CompList;