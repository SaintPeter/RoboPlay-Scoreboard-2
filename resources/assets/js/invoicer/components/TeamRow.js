import React, {Component} from 'react';
import {connect} from 'react-redux';

class TeamRowApp extends Component {

  render() {
    let invoice = this.props.rowData;

    if (invoice.team_count > 0) {
      return <tr key={"teams_" + this.props.invoiceId}>
        <td colSpan="8" className="team_section" id="teams{{ $invoice->id }}">
          <table className="table pull-right">
            <thead>
            <tr>
              <th>Team Name</th>
              <th>Division</th>
              <th className="text-center">Students</th>
              <th>Status</th>
            </tr>
            </thead>
            <tbody>
            {invoice.team_data.map(team => {
              return [<tr  key={"teams_" + this.props.invoiceId + "_meta"}>
                <td>
                  {team.name}
                </td>
                <td>
                  Division Select Here
                </td>
                <td className="text-center">{team.student_count}</td>
                <td>
                  Checked Button Here
                </td>
              </tr>,
                team.student_count ?
                  <tr  key={"teams_" + this.props.invoiceId + "_students"}>
                    <td>{null}</td>
                    <td>
                      <table className="table table-condensed">
                        <thead>
                        <tr>
                          <th>Student</th>
                          <th>Math Level</th>
                          <th className="text-center">Math Div</th>
                          <th className="text-center">T-Shirt</th>
                        </tr>
                        </thead>
                        <tbody>
                        {team.students.map(student => {
                          return <tr key={student.id}>
                            <td>{student.name}</td>
                            <td>{student.math_level_name}</td>
                            <td className="text-center">{student.math_level}</td>
                            <td className="text-center">{student.tshirt}</td>
                          </tr>
                        })
                        }
                        </tbody>
                      </table>
                    </td>
                    <td colSpan={2}>&nbsp;</td>
                  </tr>
                  :
                  null
              ]
            })}
            </tbody>
          </table>
        </td>
      </tr>
    } else {
      return null;
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

const TeamRow = connect(
  mapStateToProps,
  mapDispatchToProps
)(TeamRowApp);

export default TeamRow;