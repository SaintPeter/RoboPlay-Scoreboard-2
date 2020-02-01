import React, {Component} from 'react';
import {connect} from 'react-redux';

class PaidButtonApp extends Component {

  render() {
    switch(this.props.paid) {
      case 0: // Unpaid
        return <button
          onClick={this.props.onClick}
          className={'btn btn-xs btn-danger'}>
          Unpaid
        </button>
      case 1: // Paid
        return <button
          onClick={this.props.onClick}
          className={'btn btn-xs btn-success'}>
          Paid
        </button>
      case 2: // Pending
        return <button
          onClick={this.props.onClick}
          className={'btn btn-xs btn-warning'}>
          Pending
        </button>
      case 3: // Canceled
        return <button
          onClick={this.props.onClick}
          className={'btn btn-xs btn-info'}>
          Canceled
        </button>
    }

  }
}

// Map Redux state to component props
function mapStateToProps(state) {
  return {}
}

// Map Redux actions to component props
function mapDispatchToProps(dispatch) {
  return {}
}

const PaidButton = connect(
  mapStateToProps,
  mapDispatchToProps
)(PaidButtonApp);

export default PaidButton;