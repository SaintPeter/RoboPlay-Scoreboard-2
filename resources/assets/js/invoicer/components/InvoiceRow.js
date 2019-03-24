import React, {Component} from 'react';
import {connect} from 'react-redux';
import {Button, Modal, FormGroup, ControlLabel, FormControl, ToggleButtonGroup, ToggleButton} from 'react-bootstrap';

import PaidButton from "./PaidButton"
import {toggleShowVideo} from "../reducers/showVideosList";
import {toggleShowTeam} from "../reducers/showTeamsList";
import {updatePaidNotes} from "../reducers/invoiceData";

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
    this.setState({modalNotes: e.target.value});
  };

  handleModalSaveAndClose = () => {
    this.props.updatePaidNotes(this.props.rowData.id, this.state.modalPaid, this.state.modalNotes);
    this.setState({showPaidModal: false});
  };

  render() {
    const row = this.props.rowData;
    return <tr key={row.id}>
      <td title={`Username: ${row.email}`}>
        <a href={"mailto:" + row.email} target="_blank" title="E-mail User">{row.user_name}</a>
        &nbsp;
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
            null
          }
          )
        </small>
      </td>
      <td>
        {row.school_name}
      </td>
      <TeamCount row={row} />
      <VideoCount row={row} />
      <td>T:&nbsp;{row.team_student_count}&nbsp;V:&nbsp;{row.video_student_count}</td>
      <td>{row.notes}</td>
      <td className="text-center">
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
      <td className="text-nowrap">
        <button
          className={"btn btn-xs btn-success"}
          disabled={row.videos_checked + row.videos_unchecked === 0}
          onClick={() => this.props.toggleShowVideo(row.id)}
          title={"Show Videos"}
        >Videos</button>
        &nbsp;
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

function TeamCount(props) {
  if(props.row.team_count) {
    let checked = "";
    let unchecked = "";
    if(props.row.teams_checked) {
      checked = <span style={{color: 'green'}}>{props.row.teams_checked}</span>;
    }
    if(props.row.teams_unchecked) {
      unchecked = <span style={{color: 'red'}}>{props.row.teams_unchecked}</span>;
    }

    if(checked && unchecked) {
      return <td>
        {props.row.team_count}&nbsp;({unchecked}&nbsp;/&nbsp;{checked})
      </td>
    } else if(checked) {
      return <td>
        {props.row.team_count}&nbsp;({checked})
      </td>
    } else if(unchecked){
      return <td>
        {props.row.team_count}&nbsp;({unchecked})
      </td>
    } else {
      return <td>
        {props.row.team_count}&nbsp;(0)
      </td>
    }
  } else {
    return <td>None</td>
  }
}

function VideoCount(props) {
  if(props.row.video_count) {
    let checked = "";
    let unchecked = "";
    if(props.row.videos_checked) {
      checked = <span style={{color: 'green'}}>{props.row.videos_checked}</span>;
    }
    if(props.row.videos_unchecked) {
      unchecked = <span style={{color: 'red'}}>{props.row.videos_unchecked}</span>;
    }

    if(checked && unchecked) {
      return <td>
        {props.row.video_count}&nbsp;({unchecked}&nbsp;/&nbsp;{checked})
      </td>
    } else if(checked) {
      return <td>
        {props.row.video_count}&nbsp;({checked})
      </td>
    } else if(unchecked) {
      return <td>
        {props.row.video_count}&nbsp;({unchecked})
      </td>
    } else {
      return <td>
        {props.row.video_count}&nbsp;(0)
      </td>
    }
  } else {
    return <td>None</td>
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
    updatePaidNotes: (id, paid, notes) => dispatch(updatePaidNotes(id, paid, notes))
  }
}

const InvoiceRow = connect(
  mapStateToProps,
  mapDispatchToProps
)(InvoiceRowApp);

export default InvoiceRow;