export const setInvoiceFilter = (mode) => {
  return {
    'type': 'SET_INVOICE_FILTER',
    mode
  }
};


export default function filterInvoiceBy(state = "ALL", action) {
  switch(action.type) {
    case 'SET_INVOICE_FILTER':
      return action.mode;
    default:
      return state;
  }
}