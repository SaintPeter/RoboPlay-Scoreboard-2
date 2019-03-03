import React, {Component} from 'react';
import { Link } from 'react-router-dom';
import {connect} from "react-redux";

class YearSelectApp extends Component {

  render() {
    return <div className="pull-right" style={{marginBottom: 5}}>
      <ul className="nav nav-pills">
        {
          yearList.map(thisYear => {
            return <li key={thisYear} className={(thisYear == this.props.activeYear ? 'active' : '')}>
              <Link to={"/" + thisYear}>{thisYear}</Link>
            </li>
          })
        }
    </ul>
  </div>
  }
}

// Map Redux state to component props
function mapStateToProps(state) {
  return {
    activeYear: state.activeYear
  }
}

// Map Redux actions to component props
function mapDispatchToProps(dispatch) {
  return { }
}

const YearSelect =  connect(
  mapStateToProps,
  mapDispatchToProps
)(YearSelectApp);

export default YearSelect;