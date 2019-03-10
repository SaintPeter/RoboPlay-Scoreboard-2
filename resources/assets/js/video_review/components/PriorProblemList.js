import React, {Component} from 'react';
import {connect} from 'react-redux';

import { Row, Col, Panel, ListGroup, Button} from 'react-bootstrap';
import { formatTimestamp, lookupDetailType, formatTime } from "../utils";

class PriorProblemListApp extends Component {

  lookup(problem) {
    let id = problem.video_review_details_id;
    return problemDetailList.hasOwnProperty(id) ? problemDetailList[id].reason : 'Unknown Problem';
  }

  formatTime(problem) {
    if(problem.hasOwnProperty('timestamp') && problem.timestamp >= 0) {
      return (
        <a onClick={(e) => this.props.changeTimeHandler(e, problem.timestamp)}
           className="pull-right"
           title="Go to Timestamp"
           style={{cursor: "pointer"}}
        >
          {formatTimestamp(problem.timestamp)}
        </a>
      )
    } else {
      return null;
    }
  }

  render() {
    if(Array.isArray(this.props.problems) && this.props.problems.length) {
      return <Panel>
        <Panel.Heading key={"problem_header"}>
          <Panel.Title>Prior Problems</Panel.Title>
        </Panel.Heading>
        <DetailList
          problems={this.props.problems}
          deleteHandler={this.props.deleteHandler}
          resolveHandler={this.props.resolveHandler}
          changeTimeHandler={this.props.changeTimeHandler}
        />
      </Panel>
    } else {
      return null;
    }
  }
}

const DetailList = (props) => {
  if(isAdmin) {
    return <ListGroup>
      { props.problems.map((problem, index) => {
        return <li className="list-group-item clearfix" key={index}>
          <Row>
            <Col xs={10}>
              <h4 style={{marginTop: 0, marginBottom: 0}}>{lookupDetailType(problem)}{formatTime(problem, props.changeTimeHandler)}</h4>
              {problem.comment}
            </Col>
            <Col xs={1} className="btn" onClick={props.resolveHandler} title="Mark Issue Resolved">
              <i className="fa fa-check fa-2x" style={{color: 'green'}}>{null}</i>
            </Col>
            <Col xs={1} className="btn" onClick={props.deleteHandler} title={"Delete Issue"}>
              <i className="fa fa-times fa-2x" style={{color: 'red'}}>{null}</i>
            </Col>
          </Row>
        </li>
      })}
    </ListGroup>
  } else {
    return <ListGroup>
      { this.props.problems.map((problem, index) => {
        return <li className="list-group-item" key={index}>
          <h4>{this.lookup(problem)}{this.formatTime(problem)}</h4>
          {problem.comment}
        </li>
      })}
    </ListGroup>
  }
};

// Map Redux state to component props
function mapStateToProps(state) {
  return {}
}

// Map Redux actions to component props
function mapDispatchToProps(dispatch) {
  return {}
}

const PriorProblemList = connect(
  mapStateToProps,
  mapDispatchToProps
)(PriorProblemListApp);

export default PriorProblemList;