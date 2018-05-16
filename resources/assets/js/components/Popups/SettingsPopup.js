import React, {Component} from "react";
import UIButton from "../UIButton";

export default class SettingsPopup extends Component {
    componentDidMount() {
        this.$node = $(this.refs.settingsPopup);
        this.$node.popup({
            afterclose: () => {
                this.props.onCancel();
            }
        });
    }

    shouldComponentUpdate() {
        return false;
    }

    componentWillUnmount() {
        this.$node.popup("destroy");
    }

    componentWillReceiveProps(nextProps) {
        if(!this.props.visible && nextProps.visible) {
            this.$node.popup("open");
        }

        if(this.props.visible && !nextProps.visible) {
            this.$node.popup("close");
        }
    }

    render() {
        return (
            <div ref="settingsPopup" data-role="popup" data-history='false' id="submitPopup" className="ui-corner-all">
                <div role="banner" data-role="header" data-theme="a" className="ui-corner-top ui-header ui-bar-a">
                    <h1 aria-level="1" role="heading" className="ui-title">Settings</h1>
                    <a href="#" data-rel="back" data-role="button" data-theme="a" data-icon="delete"
                       data-iconpos="notext" className="ui-btn-right ui-link ui-btn ui-icon-back ui-btn-icon-notext ui-shadow ui-corner-all"></a>
                </div>
                <div role="main" className="ui-corner-bottom ui-content center popupContent">
                    <div className="ui-body ui-body-a ui-corner-all">
                        Warning: these functions may produce additional load on the server and should only be used if directed.
                    </div>
                    <UIButton onClick={this.props.clearChallengeDataClick}>
                        Clear All Challenge Data
                    </UIButton>
                    <UIButton onClick={this.props.clearRunsClick}>
                        Clear All Runs Data
                    </UIButton>
                    <UIButton onClick={this.props.clearScoresClick}>
                        Clear All Stored Scores
                    </UIButton>
                </div>
            </div>               
        )
    }
}

SettingsPopup.defaultProps = {
    visible: false,
};
