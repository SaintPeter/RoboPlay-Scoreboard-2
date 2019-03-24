import React, {Component} from 'react';
import {connect} from "react-redux";

import {Row, Col} from "react-bootstrap";

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
    
    this.state = {
      'invoiceKeys': []
    };

    props.setActiveYear(thisYear);
    props.fetchInvoiceData(thisYear)
      .then(() => {
        this.setState({'invoiceKeys': this.filterInvoices('ALL')});
      });
  }

  componentWillReceiveProps(nextProps) {
    if(nextProps.match.params.year != this.props.match.params.year && nextProps.match.params.year) {
      // Clear out invoice keys when year changes
      this.setState({ 'invoiceKeys': []});

      this.props.setActiveYear(nextProps.match.params.year);
      this.props.fetchInvoiceData(nextProps.match.params.year).then(() => {
        this.setState({'invoiceKeys': this.filterInvoices(nextProps.filterInvoiceBy)});
      });
    }
    if(nextProps.filterInvoiceBy != this.props.filterInvoiceBy) {
      this.setState({'invoiceKeys': this.filterInvoices(nextProps.filterInvoiceBy)});
    }
  }
  
  filterInvoices = (filterBy) => {
    if(this.props.invoiceData.hasOwnProperty(this.props.activeYear)) {
      const invoiceList = this.props.invoiceData[this.props.activeYear].invoices;
      switch(filterBy) {
        case 'UNCHECKED_TEAMS':
          return Object.keys(invoiceList).filter((invoiceId) => {
            return invoiceList[invoiceId].teams_unchecked > 0;
          },{})
            .sort((a,b) => {
              return invoiceList[b].teams_unchecked - invoiceList[a].teams_unchecked;
            });
        case 'UNCHECKED_VIDEOS':
          return Object.keys(invoiceList).filter((invoiceId) => {
            return invoiceList[invoiceId].videos_unchecked > 0;
          },{})
            .sort((a,b) => {
              return invoiceList[b].videos_unchecked - invoiceList[a].videos_unchecked;
            });
        case 'ALL':
        default:
          return Object.keys(invoiceList);
      }  
    }
  };

  render() {
    let invoiceData = {};
    let vid_divisions = {};
    let team_divisions = {};
    
    if(this.props.invoiceData.hasOwnProperty(this.props.activeYear)) {
      invoiceData = this.props.invoiceData[this.props.activeYear];
      vid_divisions = this.props.invoiceData[this.props.activeYear].vid_divisions;
      team_divisions = this.props.invoiceData[this.props.activeYear].team_divisions;
    } 
    
    const teamData  = invoiceData.hasOwnProperty('team_data') ? invoiceData.team_data : {};
    const videoData = invoiceData.hasOwnProperty('video_data') ? invoiceData.video_data : {};

    if(this.state.invoiceKeys.length) {
      return <table className="table">
        <thead>
        <tr>
          <th>Teacher</th>
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
          {this.state.invoiceKeys.map(invoiceId => {
            let invoice = invoiceData.invoices[invoiceId];
            return [
              <InvoiceRow key={invoiceId} rowData={invoice} year={this.props.activeYear}/>,
              <VideoRow
                key={'video_invoice_'+invoiceId}
                invoiceId={invoiceId}
                rowData={videoData[invoiceId]}
                divData={vid_divisions}
              />,
              <TeamRow
                key={'team_invoice_'+invoiceId}
                invoiceId={invoiceId}
                rowData={teamData[invoiceId]}
                divData={team_divisions}
              />
            ]
          })}
        </tbody>
      </table>
    } else {
      if(this.props.yearLoading) {
        return <Row>
          <Col md={3} mdOffset={4}>
            <h3 className="text-center"><i className="fa fa-spinner fa-pulse fa-fw">{null}</i> Loading . . .</h3>
          </Col>
        </Row>
      } else {
        return <Row>
          <Col md={3} mdOffset={4}>
            <h3 className="text-center">No Data for this Year</h3>
          </Col>
        </Row>
      }
    }
  }
}

// Map Redux state to component props
function mapStateToProps(state) {
  return {
    activeYear: state.activeYear,
    invoiceData: state.invoiceData,
    showVideosList: state.showVideosList,
    showTeamsList: state.showTeamsList,
    filterInvoiceBy: state.filterInvoiceBy,
    yearLoading: state.yearLoading,
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