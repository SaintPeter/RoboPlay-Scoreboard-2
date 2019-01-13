import React, {Component} from 'react';
import { Link } from 'react-router-dom';
import {connect} from "react-redux";
import {updateBackButton, updatePageTitle} from "../../scorer/actions/Generic";
import {setActiveYear} from "../reducers/activeYear";
import {fetchInvoiceData} from "../reducers/invoiceData";

class InvoiceListApp extends Component {
  constructor(props) {
    super(props);
    let thisYear = 0;

    // Update selected year
    if(props.match.params.hasOwnProperty('year') && props.match.params.year) {
      thisYear = props.match.params.year;
    } else {
      // Otherwise select the most recent year
      thisYear = yearList[yearList.length - 1]
    }

    props.setActiveYear(thisYear);
    props.fetchInvoiceData(thisYear);
  }

  componentWillReceiveProps(nextProps) {
    if(nextProps.match.params.year != this.props.match.params.year && nextProps.match.params.year) {
      this.props.setActiveYear(nextProps.match.params.year);
      this.props.fetchInvoiceData(thisYear);
    }
  }

  render() {
    const invoiceData = this.props.invoiceData[this.props.activeYear] ? this.props.invoiceData[this.props.activeYear] : [];

    if(invoiceData.length) {
      return <table className="table">
        <thead>
        <tr>
          <th>Teacher</th>
          <th>Username</th>
          <th>School</th>
          <th>Teams</th>
          <th>Videos</th>
          <th className="text-center">Students</th>
          <th>Invoice</th>
          <th>Paid</th>
          <th className="text-center">Actions</th>
        </tr>
        </thead>
        <tbody>
          {invoiceData.map(invoice => {
            return <InvoiceRow key={invoice.id} rowData={invoice} year={this.props.activeYear}/>
          })}
        </tbody>
      </table>
    } else {
      return <div>No Data for this Year</div>
    }
  }
}

class InvoiceRow extends Component {
  render() {
    const row = this.props.rowData;
    return <tr key={row.id}>
      <td>
        <a href={"mailto:" + row.email} target="_blank" title="E-mail User">{row.user_name}</a>

        <small style={{'whiteSpace': 'nowrap'}}>(
          <a href={"/switch_user/" + row.user_id} title="Switch to this User">
            <i className="fa fa-arrow-circle-right"></i>
          </a>
          &nbsp;/&nbsp;
          <a href={"http://c-stem.ucdavis.edu/wp-admin/user-edit.php?user_id=" + row.user_id}
             title="Edit User's Wordpress Profile" target="_blank">
            <i className="fa fa-pencil"></i>
          </a>
          {(this.props.year >= 2017) ? <span>
            &nbsp;/&nbsp;
            <a
            href={"http://c-stem.ucdavis.edu/wp-admin/admin.php?page=formidable-entries&frm_action=edit&id=" + row.remote_id}
            title="Edit Formidable Invoice" target="_blank">
            <i className="fa fa-file-text"></i>
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
      <td>{row.teams_unchecked}&nbsp;/&nbsp;{row.teams_checked}&nbsp;/&nbsp;{row.team_count}</td>
      <td>{row.videos_unchecked}&nbsp;/&nbsp;{row.videos_checked}&nbsp;/&nbsp;{row.video_count}</td>
      <td></td>
      <td>{row.notes}</td>
      <td>{row.paid}</td>
    </tr>
  }
}

// Map Redux state to component props
function mapStateToProps(state) {
  return {
    activeYear: state.activeYear,
    invoiceData: state.invoiceData,
  }
}

// Map Redux actions to component props
function mapDispatchToProps(dispatch) {
  return {
    setActiveYear: (newYear) => dispatch(setActiveYear(newYear)),
    fetchInvoiceData: (year) => dispatch(fetchInvoiceData(year))
  }
}

const InvoiceList =  connect(
  mapStateToProps,
  mapDispatchToProps
)(InvoiceListApp);

export default InvoiceList;