import React, {useEffect, useState} from 'react';
import {View, Text, ScrollView, Linking, Button, Image, Dimensions, StyleSheet} from "react-native";
import {SafeAreaView} from "react-native-safe-area-context";
import Style from "../Style";
import {useDispatch, useSelector} from "react-redux";
import {fetchComic} from "../redux/actions";

const styles = StyleSheet.create({
  authorRow: {
    flex: 1,
    flexDirection: "row",
    alignItems: 'flex-start',
  },
  authorHomepage: {
    marginLeft: 5,
  },
  comicNav: {
    flex: 1,
    flexDirection: "row",
    alignItems: 'flex-start',
    justifyContent: 'center',
    alignSelf: 'flex-start',
    alignContent: 'center',
    paddingBottom: 5,
  },
  noImage: {
    textAlign: 'center',
    padding: 20,
    borderWidth: 1,
    borderColor: '#dddddd',
    borderStyle: 'dashed',
    borderRadius: 1,
  }
});

const ComicScreen = function({navigation, route}) {
  const dispatch = useDispatch();

  const [_id, set_id] = useState(route.params?._id);
  const [index, setIndex] = useState(route.params?.index);
  const comic = useSelector(state => state.comic);

  useEffect(() => {
    dispatch(fetchComic(_id, index));
  }, [_id, index]);

  const requestIndex = function(index) {
    // What a dumbass problem, I am forced to do this because some weird
    // thing in thunk means that updating local state triggers even the
    // function to return a promise but not for thunk to evaluate the promise
    // until I click the anywhere on the screen again
    dispatch(fetchComic(_id, index))
  }

  const getImageStyle = function(img) {
    if (img) {
      // calculate image width and height
      const screenWidth = Dimensions.get('window').width - 20;
      const scaleFactor = img.width / screenWidth;
      const imageHeight = img.height / scaleFactor;
      const newStyle = {width: screenWidth, height: imageHeight};
      return newStyle;
    }
    return {};
  }

  return (
    <SafeAreaView>
      <ScrollView contentContainerStyle={Style.screen} contentInsetAdjustmentBehavior="automatic">
        <Text style={Style.h1}>{comic.title}</Text>
        <View style={styles.authorRow}>
          {
            comic.author &&
            <Text
              style={Style.a}
              onPress={() => Linking.openURL(comic.author_homepage)}
            >
              By {comic.author}
            </Text>
          }
          {
            comic.homepage &&
            <Text
              style={[Style.a, styles.authorHomepage]}
              onPress={() => Linking.openURL(comic.homepage)}
            >
              Homepage
            </Text>
          }
        </View>
        {
          comic.description &&
          <Text style={{marginBottom: 5}}>{comic.description}</Text>
        }
        <View style={styles.comicNav}>
          <View style={{
            flex: 1,
            flexGrow: 1,
          }} >
            {
              comic.previous &&
              <Button onPress={() => requestIndex(comic.previous.index)} title="Previous"/>
            }
          </View>
          <View style={{
            flex: 1,
            flexGrow: 1,

          }} >
            {
              comic.strip &&
              <Text style={{paddingTop: 7, textAlign: 'center'}}>{comic.strip.index}</Text>
            }
          </View>
          <View style={{
            flex: 1,
            flexGrow: 1,
          }} >
            {
              comic.next &&
              <Button onPress={() => requestIndex(comic.next.index)} title="Next"/>
            }
          </View>
        </View>
        {
          !comic.isFetching &&
          !comic.strip &&
          <Text style={[Style.a, styles.noImage]}>
            No strip found
          </Text>
        }
        {
          comic.strip && comic.strip.skip > 0 &&
          <Text style={[Style.a, styles.noImage]} onPress={() => Linking.openURL(comic.strip.url)}>
            This strip is not compatible with this site but you can click here to view it on their site
          </Text>
        }
        {
          comic.strip && comic.strip.img_src.length > 0 &&
          comic.strip.img_src.map(img => (
            <Image key={img.src} style={getImageStyle(img)} source={{
              uri: img.src,
            }}/>
          ))
        }
      </ScrollView>
    </SafeAreaView>
  );
}

export default ComicScreen;
