import React, {Component} from 'react';
import {connect} from 'react-redux';

import { Panel, ListGroup, Button, Row, Col} from 'react-bootstrap';
import { formatTimestamp, lookupDetailType, formatTime } from "../utils";

class ProblemListApp extends Component {


    render() {
    if(Array.isArray(this.props.problems) && this.props.problems.length) {
    return <Panel>
      <Panel.Heading key={"problem_header"}>
        <Panel.Title>Problems</Panel.Title>
      </Panel.Heading>
      <DetailList
        problems={this.props.problems}
        deleteHandler={this.props.deleteHandler}
        changeTimeHandler={this.props.changeTimeHandler}
      />
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

const DetailList = (props) => {
  return <ListGroup>
    { props.problems.map((problem, index) => {
      return <li className="list-group-item" key={index}>
        <Row>
          <Col xs={10}>

            <h4>{lookupDetailType(problem)}{formatTime(problem, props.changeTimeHandler)}</h4>
            {problem.comment}
          </Col>
          <Col xs={2}className="btn" onClick={() => props.deleteHandler(index)}>
            <i className="fa fa-times fa-2x" style={{color: 'red'}}>{null}</i>
          </Col>
        </Row>
      </li>
    })}
  </ListGroup>
};

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