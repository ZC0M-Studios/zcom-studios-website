#version 300 es
// =============================================================================
// ANCHOR:SHADER_ANT_FRAG
// Supports overlay modes via u_overlay_mode uniform:
//   0 = normal (colony color + carrying/state indicators)
//   1 = energy heatmap (green→yellow→red)
//   2 = state view (unique color per behavioral state)
//   3 = NN activity (turn output: blue=left, white=center, red=right)
// =============================================================================

precision mediump float;

in float v_colony;
in float v_state;
in float v_carrying;
in float v_energy;
in float v_nn_turn;
in vec2  v_local;

uniform vec3 u_colony0_color;
uniform vec3 u_colony1_color;
uniform int  u_overlay_mode;

out vec4 fragColor;

void main() {
    float dist = length(v_local);
    if (dist > 1.0) discard;

    vec3 base;

    if (u_overlay_mode == 1) {
        // ENERGY HEATMAP: green → yellow → red → dark pulsing red
        float e = clamp(v_energy, 0.0, 1.0);
        if (e > 0.6) {
            base = mix(vec3(0.9, 0.85, 0.2), vec3(0.2, 0.85, 0.3), (e - 0.6) / 0.4);
        } else if (e > 0.2) {
            base = mix(vec3(0.9, 0.2, 0.15), vec3(0.9, 0.85, 0.2), (e - 0.2) / 0.4);
        } else {
            base = vec3(0.6, 0.1, 0.1);
        }

    } else if (u_overlay_mode == 2) {
        // STATE VIEW: unique color per state
        // 0=idle(gray), 1=foraging(white), 2=returning(green), 3=attacking(red),
        // 4=following(cyan), 5=exhausted(yellow), 6=confused(magenta)
        float s = v_state;
        if (s < 0.5)      base = vec3(0.4, 0.4, 0.4);       // idle
        else if (s < 1.5)  base = vec3(0.85, 0.85, 0.85);    // foraging
        else if (s < 2.5)  base = vec3(0.2, 0.85, 0.3);      // returning
        else if (s < 3.5)  base = vec3(0.95, 0.2, 0.15);     // attacking
        else if (s < 4.5)  base = vec3(0.15, 0.8, 0.9);      // following
        else if (s < 5.5)  base = vec3(0.95, 0.85, 0.2);     // exhausted
        else               base = vec3(0.9, 0.3, 0.85);      // confused

    } else if (u_overlay_mode == 3) {
        // NN ACTIVITY: turn output magnitude
        float t = clamp(v_nn_turn, -1.0, 1.0);
        if (t < 0.0) {
            base = mix(vec3(0.85, 0.85, 0.85), vec3(0.2, 0.4, 0.95), -t);  // left = blue
        } else {
            base = mix(vec3(0.85, 0.85, 0.85), vec3(0.95, 0.3, 0.15), t);  // right = red
        }

    } else {
        // NORMAL MODE
        base = mix(u_colony0_color, u_colony1_color, v_colony);

        // Carrying food: green morsel at front
        if (v_carrying > 0.5) {
            vec2 morselPos = vec2(0.55, 0.0);
            float morselDist = length(v_local - morselPos);
            if (morselDist < 0.35) {
                float morselEdge = smoothstep(0.35, 0.15, morselDist);
                fragColor = vec4(vec3(0.3, 0.85, 0.15), morselEdge);
                return;
            }
            base = base * 0.85 + vec3(0.15, 0.12, 0.0);
        }

        // State-based tints
        float returning = step(1.5, v_state) * step(v_state, 2.5);
        base = mix(base, base + vec3(0.05, 0.12, 0.0), returning * 0.5);

        float attacking = step(2.5, v_state) * step(v_state, 3.5);
        base = mix(base, vec3(1.0, 0.9, 0.9), attacking * 0.65);
    }

    float edge = smoothstep(1.0, 0.7, dist);
    fragColor = vec4(clamp(base, 0.0, 1.0), edge);
}
