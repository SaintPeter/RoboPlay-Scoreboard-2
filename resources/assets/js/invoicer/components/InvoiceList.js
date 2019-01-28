import React, {Component} from 'react';
import { Link } from 'react-router-dom';
import {connect} from "react-redux";

import {setActiveYear} from "../reducers/activeYear";
import {fetchInvoiceData} from "../reducers/invoiceData";

import VideoRow from "./VideoRow";
import InvoiceRow from "./InvoiceRow";
import TeamRow from "./TeamRow";

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
      this.props.fetchInvoiceData(nextProps.match.params.year);
    }
  }

  render() {
    const invoiceData = this.props.invoiceData[this.props.activeYear] ? this.props.invoiceData[this.props.activeYear] : {};
    const teamData  = invoiceData.hasOwnProperty('team_data') ? invoiceData.team_data : {};
    const videoData = invoiceData.hasOwnProperty('video_data') ? invoiceData.video_data : {};

    if(invoiceData.hasOwnProperty('invoices') && Object.keys(invoiceData.invoices).length) {
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
          {Object.entries(invoiceData.invoices).map(invoice => {
            return [
              <InvoiceRow key={invoice[0]} rowData={invoice[1]} year={this.props.activeYear}/>,
              <VideoRow
                key={'video_invoice_'+invoice[0]}
                invoiceId={invoice[0]}
                rowData={videoData[invoice[1].id]}
                divData={invoiceData.vid_divisions}
              />,
              <TeamRow
                key={'team_invoice_'+invoice[0]}
                invoiceId={invoice[0]}
                rowData={teamData[invoice[1].id]}
                divData={invoiceData.team_divisions}
              />
            ]
          })}

        </tbody>
      </table>
    } else {
      return <div>No Data for this Year</div>
    }
  }
}




// Map Redux state to component props
function mapStateToProps(state) {
  return {
    activeYear: state.activeYear,
    invoiceData: state.invoiceData,
    showVideosList: state.showVideosList,
    showTeamsList: state.showTeamsList
  }
}

// Map Redux actions to component props
function mapDispatchToProps(dispatch) {
  return {
    setActiveYear: (newYear) => dispatch(setActiveYear(newYear)),
    fetchInvoiceData: (year) => dispatch(fetchInvoiceData(year)),
  }
}

const InvoiceList =  connect(
  mapStateToProps,
  mapDispatchToProps
)(InvoiceListApp);

export default InvoiceList;