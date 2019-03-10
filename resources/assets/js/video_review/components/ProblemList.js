import React, {Component} from 'react';
import {connect} from 'react-redux';

import { Panel, ListGroup, Button} from 'react-bootstrap';
import { formatTimestamp } from "../utils";

class ProblemListApp extends Component {

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

  listProblems() {
    return <ListGroup>
      { this.props.problems.map((problem, index) => {
        return <li className="list-group-item" key={index}>
          <h4>{this.lookup(problem)}{this.formatTime(problem)}</h4>
          {problem.comment}
          </li>
      })}
    </ListGroup>
  }

  render() {
    if(Array.isArray(this.props.problems) && this.props.problems.length) {
    return <Panel>
      <Panel.Heading key={"problem_header"}>
        <Panel.Title>Problems</Panel.Title>
      </Panel.Heading>
      {this.listProblems()}
      <Panel.Footer key={"problems_footer"} className="text-right">
        <Button bsStyle="info" onClick={this.props.cancelHandler}>Cancel</Button>
        &nbsp;
        <Button bsStyle="warning" onClick={this.props.saveHandler}>Save Problems</Button>
      </Panel.Footer>
    </Panel>

    } else {
      return <Panel>
        <Panel.Heading key={"problem_header"}>
          <Panel.Title>Problems</Panel.Title>
        </Panel.Heading>
        <Panel.Body key={"problems_body"}>
          No Problems Yet
        </Panel.Body>
        <Panel.Footer key={"problems_footer"} className="text-right">
          <Button bsStyle="info" onClick={this.props.cancelHandler}>Cancel</Button>
          &nbsp;
          <Button bsStyle="success" onClick={this.props.saveHandler}>Save No Problems</Button>
        </Panel.Footer>
      </Panel>
    }
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