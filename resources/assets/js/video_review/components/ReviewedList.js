import React, {Component} from 'react';
import { Panel, PanelGroup, ListGroup, Button } from 'react-bootstrap';

export class ReviewedList extends Component {

  render() {
    if(this.props.loading) {
      return <Panel key="Reviewed_Videos">
        <Panel.Heading>
          <Panel.Title>Reviewed Videos</Panel.Title>
        </Panel.Heading>
        <Panel.Body>
          <i className="fa fa-spinner fa-pulse fa-fw">{null}</i> Loading . . .
        </Panel.Body>
      </Panel>
    } else {
      if(Array.isArray(this.props.list) && this.props.list.length === 0) {
        return <Panel key="Reviewed_Videos">
          <Panel.Heading>
            <Panel.Title>Reviewed Videos</Panel.Title>
          </Panel.Heading>
          <Panel.Body>
            No Reviewed Videos
          </Panel.Body>
        </Panel>
      } else {
        const reviews = this.props.list.reduce((carry, video) => {
          const status = reviewStatuses[video.review_status];
          if(!carry.hasOwnProperty(status)) {
            carry[status] = [];
          }
          carry[status].push(video);
          return carry;
        },{});

        return <PanelGroup id={1}>
          { Object.keys(reviews).map(cat => {
              return <Panel key={cat}>
                <Panel.Heading>
                  <Panel.Title>
                    {cat}
                  </Panel.Title>
                </Panel.Heading>
                  <ListGroup componentClass="div">
                    <VideoList list={reviews[cat]} editHandler={this.props.editVideoHandler} />
                  </ListGroup>
              </Panel>
          })}
        </PanelGroup>
      }
    }
  }
}

const IssueCount = (props) => {
  const len = props.problems.length;
  const stats = props.problems.reduce((carry, problem) => {
    carry[problem.resolved ? 'resolved' : 'unresolved']++;
    return carry;
  }, {unresolved: 0, resolved: 0});

  if(stats.unresolved || stats.resolved) {
    return  <span>
      {stats.unresolved} Unresolved, {stats.resolved} Resolved
    </span>
  }
  return null;
};

class VideoList extends Component {
  render() {
    const headingStyle = {
      marginTop: 0,
      marginBottom: 0
    };
    if(isAdmin) {
      return this.props.list.map(video => {
        return <Button className="list-group-item clearfix" key={video.id} onClick={() => this.props.editHandler(video.id)}>
          <h4 style={headingStyle}>{video.name}</h4>
          <IssueCount problems={video.problems}/>
          <span className="pull-right">Reviewer: {video.reviewer.name}</span>
        </Button>
      })
    } else {
      return this.props.list.map(video => {
        return <Button className="list-group-item clearfix" key={video.id} onClick={() => this.props.editHandler(video.id)}>
          <h4 style={headingStyle}>{video.name}</h4>
          <IssueCount problems={video.problems}/>
        </Button>
      })
    }
  }
}