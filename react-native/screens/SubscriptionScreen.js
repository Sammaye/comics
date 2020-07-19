import React, {useEffect, useState} from 'react';
import {Text, TextInput, FlatList, View, Button, StyleSheet} from "react-native";
import {SafeAreaView} from "react-native-safe-area-context";
import AwesomeDebouncePromise from "awesome-debounce-promise";
import Style from "../Style";
import {useDispatch, useSelector} from "react-redux";
import {
  addSubscription,
  addSubscriptionsSubscription,
  fetchSubscriptions,
  removeSubscription,
  removeSubscriptionsSubscription
} from "../redux/actions";
import {useAsync} from "react-async-hook";
import useConstant from "use-constant";

const styles = StyleSheet.create({
  subscriptionItem: {
    flex: 1,
    flexDirection: "row",
    alignItems: 'flex-start',
    justifyContent: 'center',
    alignSelf: 'flex-start',
    alignContent: 'center',
    padding: 10,
  },
  subscriptionName: {
    fontSize: 18,
    paddingTop: 4,
  },
  searchTextInput: {
    borderRadius: 0,
    borderTopWidth: 0,
    borderLeftWidth: 0,
    borderRightWidth: 0,
    padding: 5,
  }
});

const SubscriptionScreen = function ({route, navigation}) {
  const dispatch = useDispatch();
  const auth = useSelector(state => state.auth);
  const subscriptions = useSelector(state => state.subscriptions);

  // This is a bit dodgy, it works, but it will not cancel previous actions in process
  // which means if it detects two fires it will run two fires even if another fire has
  // taken over the old one it will just receive them in order, it might be that using hooks
  // with additional plugins will help, but I wanted to avoid too much bloat here
  // THIS IS OLD CODE LEFT HERE FOR REFERENCE
  //const searchSubscriptions = text => dispatch(fetchSubscriptions(auth.token, text));
  //const searchSubscriptionsDebounced = AwesomeDebouncePromise(searchSubscriptions, 500);
  //useEffect(() => {searchSubscriptionsDebounced(textSearch)}, [textSearch]);

  const useDebouncedSearch = (searchFunction) => {
    // Handle the input text state
    const [textSearch, onChangeTestSearchText] = useState('');

    // Debounce the original search async function
    const debouncedSearchFunction = useConstant(() =>
      AwesomeDebouncePromise(searchFunction, 300)
    );

    const search = useAsync(
      async () => {
        return debouncedSearchFunction(textSearch);
      },
      [textSearch]
    );

    // Return everything needed for the hook consumer
    return {
      textSearch,
      onChangeTestSearchText,
      search,
    };
  };
  const useSearchSubscriptions = () => useDebouncedSearch(text => dispatch(fetchSubscriptions(auth.token, text)));

  const {textSearch, onChangeTestSearchText, searchResults} = useSearchSubscriptions();

  useEffect(() => {
    const listEvent = navigation.addListener('focus', () => {
      // I'm leaving this out for the minute, it is ok since it doesn't break UX
      //dispatch(fetchSubscriptions(auth.token));
    });

    return listEvent;
  }, [navigation]);

  const addSubscriptionEvent = comic_id => {
    dispatch(addSubscription(auth.token, comic_id))
      .then(response => dispatch(addSubscriptionsSubscription(comic_id)));
  }

  const removeSubscriptionEvent = comic_id => {
    dispatch(removeSubscription(auth.token, comic_id))
      .then(response => dispatch(removeSubscriptionsSubscription(comic_id)));
  }

  const SubscriptionItem = function({_id, title, subscribed, subscribed_date}) {
    return (
      <View style={styles.subscriptionItem}>
        <View style={{
          flex: 2,
          flexGrow: 2,
        }} >
          <Text style={styles.subscriptionName}>{title}</Text>
        </View>
        <View style={{
          flex: 1,
          flexGrow: 1,
        }} >
          {subscribed && <Button color="#dc3545" title="Unsubscribe" onPress={() => removeSubscriptionEvent(_id)}/>}
          {!subscribed && <Button title="Subscribe" onPress={() => addSubscriptionEvent(_id)}/>}
        </View>
      </View>
    );
  }

  // SafeAreView causes issues with the ListView not being able to calculate screen height correctly
  return (
    <>
      <TextInput
        style={[Style.textInput, styles.searchTextInput]}
        onChangeText={text => onChangeTestSearchText(text)}
        value={textSearch}
        placeholder="Search here..."
      />
      {subscriptions.subscriptions.length > 0 && (
        <FlatList
          data={subscriptions.subscriptions}
          keyExtractor={item => item._id}
          renderItem={({item}) => <SubscriptionItem {...item}/>}
        />
      )}
    </>
  );
}

export default SubscriptionScreen;
