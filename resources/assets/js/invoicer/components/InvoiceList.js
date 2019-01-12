import React, {Component} from 'react';
import { Link } from 'react-router-dom';
import {connect} from "react-redux";
import {updateBackButton, updatePageTitle} from "../../scorer/actions/Generic";

class InvoiceListApp extends Component {

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
  return { }
}

const InvoiceList =  connect(
  mapStateToProps,
  mapDispatchToProps
)(InvoiceListApp);

export default InvoiceList;