import React from 'react';
import ReactDOM from 'react-dom';
import $ from 'jquery';

export default class Forecast extends React.Component {

    constructor (props) {
        super(props);

        this.changeDay = this.changeDay.bind(this);
        this.changeLocation = this.changeLocation.bind(this);

        this.state = {
            user: props.user,
            data: null,
            day: 'loading...',
            forecast: "loading...",
            location: "loading...",
        };
    }
    componentDidMount () {
        $.ajax({
            url: route('weather.get-weather', {id: this.state.user}),
            dataType: "json",
            success: function (data) {
                console.log(data);
                this.setState({
                    data: data,
                    location: data.location,
                    day: 'Today',
                    forecast: data.current
                });
            },
            error: function (xhr, status, err) {
                console.error(xhr, status, err.toString());
            },
            context: this,
        });
    }

    changeDay (e) {
        var selectedDay = null;

        $('input[name="day"]').each(function (index, element) {
            if ($(element).is(':checked')) {
                selectedDay = $(element).val();
            }
        });

        this.setState({
            day: selectedDay,
            forecast: this.state.data[selectedDay]
        });
    }

    changeLocation (e) {
        var location = $('input#location').val();

        $.ajax({
            url: route('weather.get-weather-by-location', {id: this.state.user, location: location}),
            dataType: "json",
            success: function (data) {
                console.log(data);
                this.setState({
                    data: data,
                    location: data.location,
                    day: 'Today',
                    forecast: data.current
                });
            },
            error: function (xhr, status, err) {
                console.error(xhr, status, err.toString());
            },
            context: this,
        });
    }

    render() {
        return (
            <div className="container-fluid m-0 p-0">
                <div className="row m-0 pb-3 p-0 border-bottom">
                    <div className="col">
                        <input type="text" className="w-100" id="location" name="location" placeholder={this.state.location}/>
                    </div>
                    <div className="col text-right">
                        <a href="#" className="btn btn-primary" onClick={this.changeLocation}>Submit</a>
                    </div>
                </div>
                <div className="row m-0 mb-3 p-0">
                    <div className="col">
                        <label htmlFor="current_day">Today</label>
                        <input type="radio" id="current_day" name="day" value="current" onChange={this.changeDay}/>
                    </div>
                    <div className="col">
                        <label htmlFor="monday_day">Monday</label>
                        <input type="radio" id="monday_day" name="day" value="monday" onChange={this.changeDay} />
                    </div>
                    <div className="col">
                        <label htmlFor="tuesday_day">Tuesday</label>
                        <input type="radio" id="tuesday_day" name="day" value="tuesday" onChange={this.changeDay} />
                    </div>
                    <div className="col">
                        <label htmlFor="wednesday_day">Wednesday</label>
                        <input type="radio" id="wednesday_day" name="day" value="wednesday" onChange={this.changeDay} />
                    </div>
                    <div className="col">
                        <label htmlFor="thursday_day">Thursday</label>
                        <input type="radio" id="thursday_day" name="day" value="thursday" onChange={this.changeDay} />
                    </div>
                    <div className="col">
                        <label htmlFor="friday_day">Friday</label>
                        <input type="radio" id="friday_day" name="day" value="friday" onChange={this.changeDay} />
                    </div>
                    <div className="col">
                        <label htmlFor="saturday_day">Saturday</label>
                        <input type="radio" id="saturday_day" name="day" value="saturday" />
                    </div>
                    <div className="col">
                        <label htmlFor="sunday_day">Sunday</label>
                        <input type="radio" id="sunday_day" name="day" value="sunday" />
                    </div>
                </div>
                <table className="table">
                    <tbody>
                        <tr>
                            <th scope="row">Location</th>
                            <td className="text-capitalize">{this.state.location}</td>
                        </tr>
                        <tr>
                            <th scope="row">Day</th>
                            <td className="text-capitalize">{this.state.day}</td>
                        </tr>
                        <tr>
                            <th scope="row">Forecast</th>
                            <td className="text-capitalize">{this.state.forecast}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        );
    }
}

if (document.getElementById('user-forecast')) {
    var user = document.getElementById('user-forecast').getAttribute('user');
    ReactDOM.render(<Forecast user={user} />, document.getElementById('user-forecast'));
}
