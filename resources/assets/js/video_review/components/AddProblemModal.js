import React, {Component} from 'react';
import {connect} from 'react-redux';

import { Modal, Button, FormGroup, FormControl, ControlLabel } from 'react-bootstrap';

class AddProblemModalApp extends Component {

  constructor(props) {
    super(props);

    this.state = {
      problemData: {}
    }
  }

  saveProblem() {
    console.log("Problem Data:", this.state.problemData);
    this.props.addProblemHandler(this.state.problemData);
    this.props.hideHandler();
  }

  changeHandler(e, fieldName) {
    this.setState({
      problemData: Object.assign({}, this.state.problemData, {
        [fieldName]: e.target.value
      })
    })
  }

  problemList() {
    return [
      <option value={-1} key={-1}>-- Select Problem --</option>,
      problemList.map((cat, index) => {
      return <optgroup label={cat.name} key={cat.id}>
        { problemList[index].details.map(detail => {
            return <option value={detail.id} key={detail.id}>{detail.reason}</option>
        })}
      </optgroup>
    })]
  }

  render() {
    return <Modal show={this.props.show} onHide={(e) => this.props.hideHandler(e)}>
      <Modal.Header>
        <Modal.Title>
          Add Problem
        </Modal.Title>
      </Modal.Header>
      <Modal.Body>
        <form>
          <FormGroup>
            <ControlLabel>Type</ControlLabel>
            <FormControl componentClass="select" onChange={(e) => this.changeHandler(e, 'video_review_details_id')}>
              {this.problemList()}
            </FormControl>
          </FormGroup>
          <FormGroup>
            <ControlLabel>Comment</ControlLabel>
            <FormControl componentClass="textarea" onChange={(e) => this.changeHandler(e, 'comment')} />
          </FormGroup>
        </form>
      </Modal.Body>
      <Modal.Footer>
        <Button bsStyle="info" onClick={(e) => this.props.hideHandler(e)}>Cancel</Button>
        <Button bsStyle="primary" onClick={() => this.saveProblem()}>Save</Button>
      </Modal.Footer>
    </Modal>
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

const AddProblemModal = connect(
  mapStateToProps,
  mapDispatchToProps
)(AddProblemModalApp);

export default AddProblemModal;