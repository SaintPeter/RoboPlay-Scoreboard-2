import React, {Component} from 'react';
import {connect} from 'react-redux';

import {setActiveYear} from "../reducers/activeYear";
import {loadReviewStatus} from "../reducers/reviewStatus";
import { ReviewedList } from "./ReviewedList";

import { Button, Col, Row, Panel } from 'react-bootstrap'

class ReviewSelectorApp extends Component {
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
      fetchingVideo: false,
      fetchingReviewed: true,
      reviewedVideos: []
    };

    props.setActiveYear(thisYear);
    props.loadReviewStatus();
    this.loadReviewedVideos(thisYear, true);
  }

  componentWillReceiveProps(nextProps) {
    if(nextProps.match.params.year != this.props.match.params.year && nextProps.match.params.year) {
      this.props.setActiveYear(nextProps.match.params.year);
      this.props.loadReviewStatus();
      this.loadReviewedVideos(nextProps.match.params.year);
    }
  }

  loadReviewedVideos(year, skip_setstate = false) {
    let url = '';
    if(isAdmin) {
      console.log("Fetching All Reviewed Videos");
      url = `/api/video_review/all_reviewed_videos/${year}`;
    } else {
      console.log("Fetching Reviewed Videos for user: ", $userId);
      url = `/api/video_review/reviewed_videos/${year}/${userId}`;
    }

    if(!skip_setstate) {
      this.setState({
        fetchingReviewed: true,
      });
    }

    window.axios.get(url)
      .then((response) => {
        console.log("Videos Fetched");
        return response.data;
      })
      .then((data) => {
        this.setState({
          fetchingReviewed: false,
          reviewedVideos: data
        });
      });
  }

  fetchReviewVideo = (e) => {
    this.setState({ fetchingVideo: true });

    window.axios.get(`/api/video_review/${this.props.activeYear}/get_next`)
      .then((response) => {
        console.log("Video Response Fetched");
        this.setState({ fetchingVideo: false });
        return response.data;
      })
      .then((data) => {
        if(data.error) {
          console.log("Video Response had and error, oh noes!\n" + data.message)
        } else {
          this.props.history.push(`/${this.props.activeYear}/${data.id}`)
        }
      })
      .catch((err) => {
        console.log('Error Occured while fetching video: ' + err)
      });
  };

  editVideoHandler = (id) => {
    this.props.history.push(`/${this.props.activeYear}/${id}`)
  };

  render() {
    return [
      <Row key="review_video_top">
        <Col md={3}>&nbsp;</Col>,
        <Col md={3}>
          <Panel>
            <Panel.Heading>
              Review
            </Panel.Heading>
            <Panel.Body>
              <div className="text-center">
                <Button bsStyle="primary" onClick={this.fetchReviewVideo}>
                  Review Video &nbsp;
                  {
                    this.state.fetchingVideo ?
                      <i className="fa fa-spinner fa-pulse fa-fw">{null}</i>
                      :
                      null
                  }
                </Button>
              </div>
            </Panel.Body>
          </Panel>
        </Col>
        <Col md={2}>
          <StatusTable data={this.props.reviewStatus} year={this.props.activeYear}/>
        </Col>
        <Col md={3}>&nbsp;</Col>
      </Row>,
      <Row key="review_video_review_list">
        <Col md={6} mdOffset={3}>
          <ReviewedList
            list={this.state.reviewedVideos}
            loading={this.state.fetchingReviewed}
            editVideoHandler={this.editVideoHandler}
          />
        </Col>
      </Row>
    ]
  }
}

class StatusTable extends Component {

  render() {
    const data = (this.props.hasOwnProperty('data') &&
      this.props.data.hasOwnProperty(this.props.year)) ? this.props.data[this.props.year] : null;

    if(data) {
      return <Panel>
        <Panel.Heading>
          Statuses
        </Panel.Heading>
        <Panel.Body>
          <table className="statusTable">
            <thead>
            <tr>
              <th>Status</th>
              <th>Count</th>
            </tr>
            </thead>
            <tbody>
            <tr>
              <td>Unreviewed</td>
              <td>{data.unreviewed}</td>
            </tr>
            <tr>
              <td>Reviewed</td>
              <td>{data.reviewed}</td>
            </tr>
            <tr>
              <td>Disqualified</td>
              <td>{data.disqualified}</td>
            </tr>
            <tr>
              <td>Passed</td>
              <td>{data.passed}</td>
            </tr>
            </tbody>
          </table>
        </Panel.Body>
      </Panel>
    } else {
      return <Panel>
        <Panel.Heading>
          Statuses
        </Panel.Heading>
        <Panel.Body>
          Loading . . .
        </Panel.Body>
      </Panel>
    }
  }
}

// Map Redux state to component props
function mapStateToProps(state) {
  return {
    activeYear: state.activeYear,
    reviewStatus: state.reviewStatus
  }
}

// Map Redux actions to component props
function mapDispatchToProps(dispatch) {
  return {
    setActiveYear: (year) => dispatch(setActiveYear(year)),
    loadReviewStatus: () => dispatch(loadReviewStatus())
  }
}

const ReviewSelector = connect(
  mapStateToProps,
  mapDispatchToProps
)(ReviewSelectorApp);

export default ReviewSelector;