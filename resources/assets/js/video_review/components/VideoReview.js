import React, {Component} from 'react';
import {connect} from 'react-redux';

import YouTube from 'react-youtube';
import { Col } from 'react-bootstrap';

import {setActiveYear} from "../reducers/activeYear";

class VideoReviewApp extends Component {
  constructor(props) {
    super(props);

    this.state = {
      loading: true,
      video: {},
      error: false,
      message: ""
    };

    if(!this.props.activeYear) {
      props.setActiveYear(props.match.params.year);
    }

    this.fetchVideo(props.match.params.year, props.match.params.id)
  }

  fetchVideo(year, id) {
    console.log("Fetching Video: " + id);
    window.axios.get(`/api/video_review/${year}/fetch_video/${id}`)
      .then((response) => {
        console.log("Video Fetched");
        this.setState({ loading: false });
        return response.data;
      })
      .then((data) => {
        if(data.error) {
          this.setState({error: true, message: data.message});
          console.log("Video Response had and error, oh noes!\n" + data.message)
        } else {
          this.setState({ video: data });
        }
      })
      .catch((err) => {
        console.log('Error Occurred while fetching video: ' + err)
      });
  }

  render() {
    if(this.state.loading) {
      return <Col xs={12}>
        <h2 className="text-center">
          Loading . . .<br />
          <i className="fa fa-spinner fa-pulse fa-fw">{null}</i>
        </h2>
      </Col>
    } else if(this.state.error) {
      return <Col xs={12}>
        <h2 className="text-center">
          Error Loading Video
        </h2>
      </Col>
    } else {
      return <Col xs={12}>
        <pre>
          { JSON.stringify(this.state.video, null, 2) }
        </pre>
      </Col>
    }

  }
}

// Map Redux state to component props
function mapStateToProps(state) {
  return {
    activeYear: state.activeYear
  }
}

// Map Redux actions to component props
function mapDispatchToProps(dispatch) {
  return {
    setActiveYear: (year) => { dispatch(setActiveYear(year)) }
  }
}

const VideoReview = connect(
  mapStateToProps,
  mapDispatchToProps
)(VideoReviewApp);

export default VideoReview;