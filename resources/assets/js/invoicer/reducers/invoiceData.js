export function setInvoiceData(year, data) {
  return {
    type: 'SET_YEAR_DATA',
    year,
    data
  }
}

export const fetchInvoiceData = (year) => (dispatch, getState) => {
  //const {InvoiceData} = getState();

  return window.axios.get('/api/invoicer/invoice_list/' + year)
    .then((response) => {
      console.log(`Invoice Data ${year} Fetched`);
      return response.data;
    })
    .then((data) => {
      console.log('Saving Invoice Data');
      dispatch(setInvoiceData(year, data));
    })
    .then(() => Promise.resolve('Data Saved'))
    .catch( (error) => {
      console.log("Error loading Invoice data from remote: " + error);
    });
};


const invoiceData = (state = {}, action) => {
  switch(action.type) {
    case 'SET_YEAR_DATA':
      return Object.assign({}, state, {[action.year]: action.data});
    default:
      return state;
  }
};

export default invoiceData;