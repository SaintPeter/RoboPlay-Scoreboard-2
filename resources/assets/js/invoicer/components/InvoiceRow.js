import React, {Component} from 'react';
import {connect} from 'react-redux';
import {Button, Modal, FormGroup, ControlLabel, FormControl, ToggleButtonGroup, ToggleButton} from 'react-bootstrap';

import PaidButton from "./PaidButton"
import {toggleShowVideo} from "../reducers/showVideosList";
import {toggleShowTeam} from "../reducers/showTeamsList";

class InvoiceRowApp extends Component {
  constructor(props) {
    super(props);

    this.state = {
      showPaidModal: false,
      modalPaid: props.rowData.paid,
      modalNotes: props.rowData.notes
    }
  }

  handleModalClose = () => {
    this.setState({
      showPaidModal: false,
      modalPaid: this.props.rowData.paid,
      modalNotes: this.props.rowData.notes
    });
  };

  handleModalShow = () => {
    this.setState({showPaidModal: true});
  };

  handleModalRadio = (newPaid) => {
    this.setState({modalPaid: newPaid});
  };

  handleModalNotes = (e) => {
    this.State({modalNotes: e.target.value});
  };

  handleModalSaveAndClose = () => {
    // TODO: Save Data
    this.setState({showPaidModal: false});
  };

  render() {
    const row = this.props.rowData;
    return <tr key={row.id}>
      <td>
        <a href={"mailto:" + row.email} target="_blank" title="E-mail User">{row.user_name}</a>

        <small style={{'whiteSpace': 'nowrap'}}>(
          <a href={"/switch_user/" + row.user_id} title="Switch to this User">
            <i className="fa fa-arrow-circle-right" />
          </a>
          &nbsp;/&nbsp;
          <a href={"http://c-stem.ucdavis.edu/wp-admin/user-edit.php?user_id=" + row.user_id}
             title="Edit User's Wordpress Profile" target="_blank">
            <i className="fa fa-pencil" />
          </a>
          {(this.props.year >= 2017) ? <span>
            &nbsp;/&nbsp;
              <a
                href={"http://c-stem.ucdavis.edu/wp-admin/admin.php?page=formidable-entries&frm_action=edit&id=" + row.remote_id}
                title="Edit Formidable Invoice" target="_blank">
            <i className="fa fa-file-text" />
            </a>
            </span>
            :
            <span></span>
          }
          )
        </small>
      </td>
      <td>
        {row.email}
      </td>
      <td>
        {row.school_name}
      </td>
      <td>{row.team_count}&nbsp;(
        <span style={{color: 'red'}}>{row.teams_unchecked}</span>
        &nbsp;/&nbsp;
        <span style={{color: 'green'}}>{row.teams_checked})</span>
      </td>
      <td>{row.video_count}&nbsp;(
        <span style={{color: 'red'}}>{row.videos_unchecked}</span>
        &nbsp;/&nbsp;
        <span style={{color: 'green'}}>{row.videos_checked})</span>
      </td>
      <td>T:&nbsp;{row.team_student_count}&nbsp;V:&nbsp;{row.video_student_count}</td>
      <td>{row.notes}</td>
      <td>
        <PaidButton paid={row.paid} onClick={this.handleModalShow}/>
        <Modal show={this.state.showPaidModal} onHide={() => this.handleModalClose(row.id)}>
          <Modal.Header>
            Set Invoice Status
          </Modal.Header>
          <Modal.Body>
            <FormGroup>
              <ToggleButtonGroup
                type="radio"
                name="modalRadio"
                value={this.state.modalPaid}
                onChange={this.handleModalRadio}
              >
                <ToggleButton value={0}>Unpaid</ToggleButton>
                <ToggleButton value={2}>Pending</ToggleButton>
                <ToggleButton value={1}>Paid</ToggleButton>
              </ToggleButtonGroup>
            </FormGroup>
            <FormGroup>
              <ControlLabel>Check #/Notes</ControlLabel>
              <FormControl
                type="text"
                placeholder="Check: 12345"
                value={this.state.modalNotes}
                onChange={this.handleModalNotes}
              />
            </FormGroup>
          </Modal.Body>
          <Modal.Footer>
            <Button onClick={this.handleModalClose}>Cancel</Button>
            <Button bsStyle="primary" onClick={this.handleModalSaveAndClose}>Save</Button>
          </Modal.Footer>
        </Modal>
      </td>
      <td>
        <button
          className={"btn btn-xs btn-success"}
          disabled={row.videos_checked + row.videos_unchecked === 0}
          onClick={() => this.props.toggleShowVideo(row.id)}
          title={"Show Videos"}
        >Videos</button>
        <button
          className={"btn btn-xs btn-info"}
          disabled={row.teams_checked + row.teams_unchecked === 0}
          onClick={() => this.props.toggleShowTeam(row.id)}
          title={"Show Teams"}
        >Teams</button>
      </td>
    </tr>
  }
}

// Map Redux state to component props
function mapStateToProps(state) {
  return {
    showVideosList: state.showVideosList,
    showTeamsList: state.showTeamsList
  }
}

// Map Redux actions to component props
function mapDispatchToProps(dispatch) {
  return {
    toggleShowVideo: (id) => dispatch(toggleShowVideo(id)),
    toggleShowTeam: (id) => dispatch(toggleShowTeam(id)),
  }
}

const InvoiceRow = connect(
  mapStateToProps,
  mapDispatchToProps
)(InvoiceRowApp);

export default InvoiceRow;