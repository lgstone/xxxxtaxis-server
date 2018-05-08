<div class="modal fade" id="tripModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Trip Detail</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="card-body">
          <table class="table">
            <tbody>
              <tr>
                <th scope="row">Start Time</th>
                <td id="startTime"></td>
              </tr>
              <tr>
                <th scope="row">End Time</th>
                <td id="endTime"></td>
              </tr>
              <tr>
                <th scope="row">Departure</th>
                <td id="dep"></td>
              </tr>
              <tr>
                <th scope="row">Destination</th>
                <td id="dest"></td>
              </tr>
              <tr>
                <th scope="row">Base Charge (NZD)</th>
                <td id="baseCharge"></td>
              </tr>
              <tr>
                <th scope="row">Price per KM (NZD)</th>
                <td id="pricePerKM"></td>
              </tr>
              <tr>
                <th scope="row">Price per Minute</th>
                <td id="pricePerMin"></td>
              </tr>
              <tr>
                <th scope="row">Price Minimum (NZD)</th>
                <td id="priceMin"></td>
              </tr>
              <tr>
                <th scope="row">Promotion Code</th>
                <td id="promotion"></td>
              </tr>
              <tr>
                <th scope="row">Rider Rating</th>
                <td id="passengerRating">
                  <div class="star-rating">
                    <div class="star-rating-top" id="passengerRatingVal" >
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                    <div class="star-rating-bottom">
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                  </div>
                </td>
              </tr>
              <tr>
                <th scope="row">Rider Comments</th>
                <td id="passengerComments"></td>
              </tr>
              <tr>
                <th scope="row">Driver Rating</th>
                <td id="driverRating">
                  <div class="star-rating">
                    <div class="star-rating-top" id="driverRatingVal">
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                    <div class="star-rating-bottom">
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                  </div>                </td>
              </tr>
              <tr>
                <th scope="row">Driver Comments</th>
                <td id="driverComments"></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>