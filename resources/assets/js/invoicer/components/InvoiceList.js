import React, {Component} from 'react';
import { Link } from 'react-router-dom';
import {connect} from "react-redux";
import {updateBackButton, updatePageTitle} from "../../scorer/actions/Generic";
import {setActiveYear} from "../reducers/activeYear";

class InvoiceListApp extends Component {
  constructor(props) {
    super(props);

    // Update selected year
    if(props.match.params.hasOwnProperty('year') && props.match.params.year) {
      props.setActiveYear(props.match.params.year);
    } else {
      // Otherwise select the most recent year
      props.setActiveYear(yearList[yearList.length - 1])
    }
  }

  componentWillReceiveProps(nextProps) {
    if(nextProps.match.params.year != this.props.match.params.year) {
      this.props.setActiveYear(nextProps.match.params.year);
    }
  }

  render() {
    return <div>This is a test</div>
  }
}

// Map Redux state to component props
function mapStateToProps(state) {
  return {}
}

// Map Redux actions to component props
function mapDispatchToProps(dispatch) {
  return {
    setActiveYear: (newYear) => dispatch(setActiveYear(newYear))
  }
}

const InvoiceList =  connect(
  mapStateToProps,
  mapDispatchToProps
)(InvoiceListApp);

export default InvoiceList;