import {StyleSheet} from 'react-native';

const Style = StyleSheet.create({
  page: {
    flexGrow: 1,
  },
  screen: {
    flexGrow: 1,
    padding: 10,
  },
  a: {
    color: 'blue',
  },
  h1: {
    fontSize: 20,
  },
  formSummaryContainer: {
    marginBottom: 15,
  },
  formSummaryLine: {
    marginBottom: 5,
  },
  formError : {
    color: 'red',
  },
  formGroup: {
    marginBottom: 15,
  },
  label: {
    marginBottom: 5,
  },
  textInput: {
    borderWidth: 1,
    borderColor: '#ced4da',
    borderRadius: 3,
    padding: 2,
  }
});

export default Style;
