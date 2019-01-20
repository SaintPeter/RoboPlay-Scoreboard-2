import React, {Component} from 'react';
import {connect} from 'react-redux';

import PaidButton from "./PaidButton"
import {toggleShowVideo} from "../reducers/showVideosList";

class InvoiceRowApp extends Component {
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
      <td><PaidButton paid={row.paid} /></td>
      <td>
        <button
          className={"btn btn-success"}
          disabled={row.video_data.length === 0}
          onClick={() => this.props.toggleShowVideo(row.id)}
          title={"Show Videos"}
        >Videos</button>
      </td>
    </tr>
  }
}

// Map Redux state to component props
function mapStateToProps(state) {
  return {
    showVideosList: state.showVideosList,
  }
}

// Map Redux actions to component props
function mapDispatchToProps(dispatch) {
  return {
    toggleShowVideo: (id) => dispatch(toggleShowVideo(id)),
  }
}

const InvoiceRow = connect(
  mapStateToProps,
  mapDispatchToProps
)(InvoiceRowApp);

export default InvoiceRow;