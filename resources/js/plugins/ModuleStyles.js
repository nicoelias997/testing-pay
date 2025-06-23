export function text() {
  return {
    fontFamily: 'default',
    fontSize: null,
    fontSizeUnits: 'px',
    color: '#000000',
    background: {
      active: false,
      value: '#ccff00'
    },
    fontShadow: {
      active: false,
      position: {
        top: 0,
        topUnits: 'px',
        left: 0,
        leftUnits: 'px'
      },
      blur: 0,
      blurUnits: 'px',
      length: 0,
      lengthUnits: 'px',
      spread: 0,
      spreadUnits: 'px',
      color: '#000000',
      applyTo: null
    },
    alignment: null,
    fontWeight: null, // This value is always a number (100, 200, 300, 400...)
    fontStyle: null,
    lineHeight: 0,
    lineHeightUnits: 'em',
    letterSpacing: 0,
    letterSpacingUnits: 'px',
    // headingLevel: 
    applyTo: null
  }
}

export function background() {
  return {
    color: {
      active: false,
      value: '#000000'
    },
    image: {
      active: false,
      url: ''
    },
    gradient: {
      active: false,
      repeat: false,
      type: 'linear',
      config: {
        direction: 0
      },
      primaryColor: '#333333',
      secondaryColor: '#cccccc'
    },
    position: {
      top: 0,
      topUnits: '%',
      left: 0,
      leftUnits: '%'
    },
    size: 'auto',
    repeat: true,
    applyTo: null
  }
}

export function border() {
  return {
    width: 0,
    units: 'px',
    color: '#000000',
    style: 'solid',
    top: false,
    right: false,
    bottom: false,
    left: false,
    radius: {
      topLeft: 0,
      topLeftUnits: 'px',
      topRight: 0,
      topRightUnits: 'px',
      bottomRight: 0,
      bottomRightUnits: 'px',
      bottomLeft: 0,
      bottomLeftUnits: 'px'
    },
    applyTo: null
  }
}

export function margin() {
  return {
    top: null,
    topUnits: 'px',
    right: null,
    rightUnits: 'px',
    bottom: null,
    bottomUnits: 'px',
    left: null,
    leftUnits: 'px',
    applyTo: null
  }
}

export function padding() {
  return {
    top: null,
    topUnits: 'px',
    right: null,
    rightUnits: 'px',
    bottom: null,
    bottomUnits: 'px',
    left: null,
    leftUnits: 'px',
    applyTo: null
  }
}

export function shadow() {
  return {
    active: false,
    position: {
      top: 0,
      topUnits: 'px',
      left: 0,
      leftUnits: 'px'
    },
    blur: 0,
    blurUnits: 'px',
    length: 0,
    lengthUnits: 'px',
    spread: 0,
    spreadUnits: 'px',
    color: '#000000',
    inset: false,
    applyTo: null,
    applyToInset: null
  }
}

export function filters() {
  return {
    hue: 0,
    saturate: 100,
    brightness: 100,
    contrast: 100,
    invert: 0,
    sepia: 0,
    opacity: 100,
    blur: 0,
    applyTo: null
  }
}

export function defaultStyles(type) {
  return {
    text: {
      ...text(),
      ...background(),
      ...border(),
      ...margin(),
      ...padding(),
      ...shadow(),
      ...filters()
    },
    image: {
      background: {
        color: '#fff'
      }
    }
  }[type]
}
