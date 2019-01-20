import React, {Component} from 'react';
import {connect} from 'react-redux';

class PaidButtonApp extends Component {

  render() {
    switch(this.props.paid) {
      case 0: // Unpaid
        return <button
          className={'btn btn-xs btn-danger'}>
          Unpaid
        </button>
      case 1: // Paid
        return <button
          className={'btn btn-xs btn-success'}>
          Paid
        </button>
      case 2: // Pending
        return <button
          className={'btn btn-xs btn-warning'}>
          Pending
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