import React, {Component} from 'react';
import {connect} from 'react-redux';

import YouTube from 'react-youtube';
import { Row, Col, Panel, Button } from 'react-bootstrap';
import update from 'immutability-helper';

import {setActiveYear} from "../reducers/activeYear";
import FileList  from "./FileList";
import AddProblemModal from "./AddProblemModal";
import ProblemList from "./ProblemList";
import ReviewDqModal from "./ReviewDqModal";

class VideoReviewApp extends Component {
  constructor(props) {
    super(props);

    this.state = {
      loading: true,
      video: {
        problems: []
      },
      error: false,
      message: "",
      showProblemModal: false,
      editMode: false,
      editIndex: -1,
      timestamp: 0,
      staticTimeStamp: 0,
      reviewDqModalVisible: false,
    };

    this.timePoll = null;
    this.playerReference = null;

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
          if(data.hasOwnProperty('problems') && Array.isArray(data.problems)) {
            data.problems = data.problems.map((item) => {
              item['written'] = true;
              return item;
            })
          }
          this.setState({ video: data });
        }
      })
      // .catch((err) => {
      //   console.log('Error Occurred while fetching video: ' + err)
      // });
  }

  showProblemModal(e) {
    //e ? e.preventDefault() : null;
    this.setState({
      editMode: false,
      editIndex: -1,
      showProblemModal: true,
      staticTimeStamp: this.state.timestamp
    });
  }

  editProblemHandler(index) {
    this.setState({
      editMode: true,
      editIndex: index,
      showProblemModal: true,
      staticTimeStamp: 0
    });
  }

  resolveProblemHandler(e, index) {
    e.stopPropagation();
    const problemId = this.state.video.problems[index].id;
    console.log(`Resolving Problem ${problemId}, Video Id: ${this.state.video.id}`);

    window.axios.get(`/api/video_review/resolve_problem/${this.state.video.id}/${problemId}`)
      .then((response) => {
        console.log('Problem Resolved');
        this.setState(update(this.state, {
          video: {
            problems: {
              [index]: { $merge: { resolved: 1, written: true } }
            }
          }
        }));
      })
      .catch((err) => {
        console.log('Error Resolving Problem: ' + err);
      })
  }

  cancelHandler() {
    console.log("Canceled Review");
    this.props.history.push(`/${this.props.activeYear}/`)
  }

  deleteHandler(e, index) {
    e.stopPropagation();
    const newState = {
      video: {
        problems: {
          $splice: [[index,1]]
        }
      }
    };
    this.setState(update(this.state,newState));
  }

  addProblemHandler(data, saveTimestamp) {
    // Default values
    data['timestamp'] = saveTimestamp ? this.state.timestamp : -1;
    data['comment'] = data.hasOwnProperty('comment') ? data['comment'] : '';

    let updateState = {};
    if(data.hasOwnProperty('id')) {
      const findIndex = this.state.video.problems.findIndex((item) => {
        return item.id == data.id;
      });
      if(findIndex > -1) {
        updateState = {
          video: {
            problems: { $splice: [[findIndex, 1, data]] }
          }
        };
      } else {
        console.log(`Error: Unable to find ID '${data.id}' of existing problem`)
      }
    } else {
      updateState = {
        video: {
          problems: { $push: [data] }
        }
      }
    }
    const newState = update(this.state, updateState);
    this.setState(newState);
  }

  changeTimeHandler(e, time, thisRef) {
    e ? e.stopPropagation() : null;
    if(thisRef.playerReference) {
      thisRef.playerReference.seekTo(time)
    }
  }

  hideProblemModal(e) {
    e ? e.preventDefault() : null;
    this.setState({showProblemModal: false})
  }

  tickHandler(e) {
    const seconds = e.target.getCurrentTime();
    if(seconds != this.state.timestamp) {
      this.setState({timestamp: seconds});
    }
  }

  readyHandler(e) {
    this.playerReference = e.target;
    if(!this.timePoll) {
      this.timePoll = setInterval(() => {
        this.tickHandler(e);
      }, 1500)
    }
  }

  // Disqualification Modal
  dqHide = (e) => {
    this.setState({ reviewDqModalVisible: false })
  };

  dqShow = (e) => {
    this.setState({ reviewDqModalVisible: true })
  };

  dqSilent = (e) => {
    this.dqHide(e);
    this.setVideoState(2);
  };

  dqSend = (e) => {
    console.log(`Sending DQ for Video ${this.state.video.id}`);
    window.axios.get(`/api/video_review/send_dq/${this.state.video.id}`)
      .then(result => {
        console.log(`Video ${this.state.video.id} Disqualification Sent`);
        this.dqHide();
      })
      .catch(err => {
        console.error('Video Disqualification Send Error: ', err);
      })
  };

  setVideoState = (newState) => {
    console.log(`Setting Video ${this.state.video.id} to ${newState}`);
    window.axios.get(`/api/video_review/set_review_status/${this.state.video.id}/${newState}`)
      .then((response) => {
        console.log(`Video ${this.state.video.id} updated to ${newState}`);
        this.props.history.push(`/${this.props.activeYear}/`)
      })
      .catch((err) => {
        console.error("Video State Set Error: " , err);
      })
  };

  componentWillUnmount() {
    clearInterval(this.timePoll);
  }

  saveProblemsHandler() {
    const unwrittenCount = this.state.video.problems.reduce((carry, problem) => {
      carry += (problem.hasOwnProperty('written') && !problem.written) ? 1 : 0;
      return carry;
    }, 0);
    if(unwrittenCount) {
      console.log(`Save unwritten ${unwrittenCount} Problems`);
      const data = this.state.video.problems.filter((problem) => {
        return !problem.written;
      });
      window.axios.post(`/api/video_review/save_problems/${this.props.match.params.id}`,{problems: data})
        .then((result) => {
          console.log("Problems for video saved");
          this.props.history.push(`/${this.props.activeYear}/`)
        })
    } else {
      console.log("Save No Problems");
      window.axios.get(`/api/video_review/save_no_problems/${this.props.match.params.id}`)
        .then((result) => {
          console.log("No Problems for video saved");
          this.props.history.push(`/${this.props.activeYear}/`)
        })
    }
  }

  render() {
    const priorProblemCount = this.state.video.hasOwnProperty('problems') ? this.state.video.problems.length : 0;
    let problemData = {};
    if(this.state.editMode) {
      problemData = this.state.video.problems[this.state.editIndex];
    }

    if(this.state.loading) {
      return <Row>
        <Col xs={12} key={"top_level_column"}>
          <h2 className="text-center">
            Loading . . .<br />
            <i className="fa fa-spinner fa-pulse fa-fw">{null}</i>
          </h2>
        </Col>
      </Row>
    } else if(this.state.error) {
      return <Row>
        <Col xs={12} key={"top_level_column"}>
          <h2 className="text-center">
            Error Loading Video
          </h2>
        </Col>
      </Row>
    } else {
      return <Row>
        <Col xs={12} key={"top_level_column"}>
          <AddProblemModal
            show={this.state.showProblemModal}
            hideHandler={(e) => this.hideProblemModal(e)}
            addProblemHandler={(data, saveTimestamp) => this.addProblemHandler(data, saveTimestamp)}
            timestamp={this.state.staticTimeStamp}
            problemData={problemData}
            editMode={this.state.editMode}
          />
          <Panel key={"video_viewer"}>
            <Panel.Heading key={"video_header"}>
              <h3 style={{marginTop: 5}} key={"video_title" + this.state.video.yt_code}>
                {this.state.video.name}
                <span className="pull-right">
                <Button onClick={(e) => this.showProblemModal(e)}>
                  <i className="fa fa-plus">{null}</i>&nbsp;
                  Add Problem
                </Button>
              </span>
              </h3>
            </Panel.Heading>
            <Panel.Body key={"video_body"}>
              <YouTube
                videoId={this.state.video.yt_code}
                opts={{width: '100%', height: '100%'}}
                className="embed-responsive-item"
                containerClassName="embed-responsive embed-responsive-16by9"
                key={"video_continer_" + this.state.video.yt_code}
                onReady={e => this.readyHandler(e)}
              />
            </Panel.Body>
          </Panel>
        </Col>
        <Col xs={12} md={6} key={"filelist_col"}>
          <FileList files={this.state.video.files}/>
        </Col>
        <Col xs={12} md={6} key={"problemlist_col"}>
          <ProblemList
            video={this.state.video}
            problems={this.state.video.problems}
            changeTimeHandler={(e, time) => this.changeTimeHandler(e, time, this)}
            cancelHandler={() => this.cancelHandler()}
            saveHandler={() => this.saveProblemsHandler()}
            showProblemModal={() => this.showProblemModal()}
            editHandler={(i) => this.editProblemHandler(i)}
            resolveProblemHandler={(e, i) => this.resolveProblemHandler(e, i)}
            deleteHandler={(e, i) => this.deleteHandler(e, i)}
          />
        </Col>
        <Col xs={6} md={3} key={"actions"} >
          { isAdmin ?
            <ReviewDqModal
              video={this.state.video}
              visible={this.state.reviewDqModalVisible}
              onHide={this.dqHide}
              onSend={this.dqSend}
              silentDq={this.dqSilent}
            />
            :
              null
          }
          { isAdmin ?
            <Panel>
              <Panel.Heading>
                <Panel.Title>Actions</Panel.Title>
              </Panel.Heading>
              <Panel.Body>
                <Button
                  bsStyle="info"
                  disabled={this.state.video.review_status === 0}
                  onClick={(e) => this.setVideoState(0)}
                  block
                >
                  Move to Unreviewed
                </Button><br />
                <Button
                  bsStyle="warning"
                  disabled={this.state.video.review_status === 1}
                  onClick={(e) => this.setVideoState(1)}
                  block
                >
                  Move to Reviewed
                </Button><br />
                <Button
                  bsStyle="danger"
                  onClick={this.dqShow}
                  disabled={this.state.video.review_status === 2}
                  block
                >
                  Send Disqualification (review)
                </Button><br />
                <Button
                  bsStyle="success"
                  disabled={this.state.video.review_status === 3}
                  onClick={(e) => this.setVideoState(3)}
                  block
                >
                  Move to Passed
                </Button>
              </Panel.Body>
            </Panel>
            :
              null
          }
        </Col>
      </Row>
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