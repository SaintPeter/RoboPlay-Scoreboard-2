import React, {Component} from 'react';
import {connect} from 'react-redux';

import { Modal, ListGroup, ListGroupItem } from 'react-bootstrap';

class ProblemListApp extends Component {

  render() {
    return <div></div>
  }
}

// Map Redux state to component props
function mapStateToProps(state) {
  return {}
}

// Map Redux actions to component props
function mapDispatchToProps(dispatch) {
  return {}
}

const ProblemList = connect(
  mapStateToProps,
  mapDispatchToProps
)(ProblemListApp);

export default ProblemList;